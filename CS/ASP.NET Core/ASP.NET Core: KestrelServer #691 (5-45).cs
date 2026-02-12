using Microsoft.AspNetCore.Connections;
using Microsoft.AspNetCore.Hosting.Server;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.AspNetCore.Server.Kestrel.Core.Internal;
using Microsoft.AspNetCore.Server.Kestrel.Core.Internal.Infrastructure;
using Microsoft.AspNetCore.Server.Kestrel.Https;
using Microsoft.AspNetCore.Server.Kestrel.Https.Internal;
using Microsoft.Extensions.Hosting;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.Server.Kestrel.Core;

/// <summary>
/// Kestrel server.
/// </summary>
public class KestrelServer : IServer
{
    private readonly KestrelServerImpl _innerKestrelServer;

    /// <summary>
    /// Initializes a new instance of <see cref="KestrelServer"/>.
    /// </summary>
    /// <param name="options">The Kestrel <see cref="IOptions{TOptions}"/>.</param>
    /// <param name="transportFactory">The <see cref="IConnectionListenerFactory"/>.</param>
    /// <param name="loggerFactory">The <see cref="ILoggerFactory"/>.</param>
    public KestrelServer(IOptions<KestrelServerOptions> options, IConnectionListenerFactory transportFactory, ILoggerFactory loggerFactory)
    {
        _innerKestrelServer = new KestrelServerImpl(
            options,
            new[] { transportFactory ?? throw new ArgumentNullException(nameof(transportFactory)) },
            Array.Empty<IMultiplexedConnectionListenerFactory>(),
            new SimpleHttpsConfigurationService(),
            loggerFactory,
            diagnosticSource: null,
            new KestrelMetrics(new DummyMeterFactory()),
            heartbeatHandlers: []);
    }

    /// <inheritdoc />
    public IFeatureCollection Features => _innerKestrelServer.Features;