// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

#endregion

using System.Diagnostics;
using Grpc.Core;
#if SUPPORT_LOAD_BALANCING
using Grpc.Net.Client.Balancer;
using Grpc.Net.Client.Balancer.Internal;
#endif
using Grpc.Net.Client.Configuration;
using Grpc.Net.Client.Internal;
using Grpc.Net.Client.Internal.Retry;
using Grpc.Net.Compression;
using Grpc.Shared;
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
