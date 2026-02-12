using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Logging.Abstractions;

namespace Grpc.Net.Client;

/// <summary>
/// Represents a gRPC channel. Channels are an abstraction of long-lived connections to remote servers.
/// Client objects can reuse the same channel. Creating a channel is an expensive operation compared to invoking
/// a remote call so in general you should reuse a single channel for as many calls as possible.
/// </summary>
[DebuggerDisplay("{DebuggerToString(),nq}")]
public sealed partial class GrpcChannel : ChannelBase, IDisposable
{
    internal const int DefaultMaxReceiveMessageSize = 1024 * 1024 * 4; // 4 MB
#if SUPPORT_LOAD_BALANCING
    internal const long DefaultInitialReconnectBackoffTicks = TimeSpan.TicksPerSecond * 1;
    internal const long DefaultMaxReconnectBackoffTicks = TimeSpan.TicksPerSecond * 120;
#endif
    internal const int DefaultMaxRetryAttempts = 5;
    internal const long DefaultMaxRetryBufferSize = 1024 * 1024 * 16; // 16 MB
    internal const long DefaultMaxRetryBufferPerCallSize = 1024 * 1024; // 1 MB

    private readonly object _lock;
    private readonly ThreadSafeLookup<IMethod, GrpcMethodInfo> _methodInfoCache;
    private readonly Func<IMethod, GrpcMethodInfo> _createMethodInfoFunc;
    private readonly Dictionary<MethodKey, MethodConfig>? _serviceConfigMethods;
    private readonly bool _isSecure;
    private readonly List<CallCredentials>? _callCredentials;
    private readonly HashSet<IDisposable> _activeCalls;

    internal Uri Address { get; }
    internal HttpMessageInvoker HttpInvoker { get; }
    internal TimeSpan? ConnectTimeout { get; }
    internal TimeSpan? ConnectionIdleTimeout { get; }
    internal HttpHandlerType HttpHandlerType { get; }
    internal TimeSpan InitialReconnectBackoff { get; }
    internal TimeSpan? MaxReconnectBackoff { get; }
    internal int? SendMaxMessageSize { get; }
    internal int? ReceiveMaxMessageSize { get; }
    internal int? MaxRetryAttempts { get; }
    internal long? MaxRetryBufferSize { get; }
    internal long? MaxRetryBufferPerCallSize { get; }
    internal ILoggerFactory LoggerFactory { get; }
    internal ILogger Logger { get; }
    internal bool ThrowOperationCanceledOnCancellation { get; }
    internal bool UnsafeUseInsecureChannelCallCredentials { get; }
    internal bool IsSecure => _isSecure;
    internal List<CallCredentials>? CallCredentials => _callCredentials;
    internal Dictionary<string, ICompressionProvider> CompressionProviders { get; }
    internal string MessageAcceptEncoding { get; }
    internal bool Disposed { get; private set; }
    internal Version HttpVersion { get; }
#if NET5_0_OR_GREATER
    internal HttpVersionPolicy HttpVersionPolicy { get; }
#endif

#if SUPPORT_LOAD_BALANCING
    // Load balancing
    internal ConnectionManager ConnectionManager { get; }

    // Set in unit tests
    internal ISubchannelTransportFactory SubchannelTransportFactory;
#endif

    // Stateful
    internal ChannelRetryThrottling? RetryThrottling { get; }
    internal long CurrentRetryBufferSize;

    // Options that are set in unit tests
    internal ISystemClock Clock = SystemClock.Instance;
    internal IOperatingSystem OperatingSystem;
    internal IRandomGenerator RandomGenerator;
    internal IDebugger Debugger;
    internal bool DisableClientDeadline;
    internal long MaxTimerDueTime = uint.MaxValue - 1; // Max System.Threading.Timer due time

    private readonly bool _shouldDisposeHttpClient;

    internal GrpcChannel(Uri address, GrpcChannelOptions channelOptions) : base(address.Authority)
    {
        _lock = new object();
        _methodInfoCache = new ThreadSafeLookup<IMethod, GrpcMethodInfo>();

        // Dispose the HTTP client/handler if...
        //   1. No client/handler was specified and so the channel created the client itself
        //   2. User has specified a client/handler and set DisposeHttpClient to true
        _shouldDisposeHttpClient = (channelOptions.HttpClient == null && channelOptions.HttpHandler == null)
            || channelOptions.DisposeHttpClient;

        Address = address;
        LoggerFactory = channelOptions.LoggerFactory ?? channelOptions.ResolveService<ILoggerFactory>(NullLoggerFactory.Instance);
        OperatingSystem = channelOptions.ResolveService<IOperatingSystem>(Internal.OperatingSystem.Instance);
        RandomGenerator = channelOptions.ResolveService<IRandomGenerator>(new RandomGenerator());
        Debugger = channelOptions.ResolveService<IDebugger>(new CachedDebugger());
        Logger = LoggerFactory.CreateLogger(typeof(GrpcChannel));

#if SUPPORT_LOAD_BALANCING
        InitialReconnectBackoff = channelOptions.InitialReconnectBackoff;
        MaxReconnectBackoff = channelOptions.MaxReconnectBackoff;

        var resolverFactory = GetResolverFactory(channelOptions);
        ResolveCredentials(channelOptions, out _isSecure, out _callCredentials);
        (HttpHandlerType, ConnectTimeout, ConnectionIdleTimeout) = CalculateHandlerContext(Logger, address, _isSecure, channelOptions);

        SubchannelTransportFactory = channelOptions.ResolveService<ISubchannelTransportFactory>(new SubChannelTransportFactory(this));

        if (!IsHttpOrHttpsAddress(Address) || channelOptions.ServiceConfig?.LoadBalancingConfigs.Count > 0)
        {
            ValidateHttpHandlerSupportsConnectivity();
        }

        var defaultPort = IsSecure ? 443 : 80;
        var resolver = resolverFactory.Create(new ResolverOptions(Address, defaultPort, LoggerFactory, channelOptions));

        ConnectionManager = new ConnectionManager(
            resolver,
            channelOptions.DisableResolverServiceConfig,
            LoggerFactory,
            channelOptions.ResolveService<IBackoffPolicyFactory>(new ExponentialBackoffPolicyFactory(RandomGenerator, InitialReconnectBackoff, MaxReconnectBackoff)),
            SubchannelTransportFactory,
            ResolveLoadBalancerFactories(channelOptions));