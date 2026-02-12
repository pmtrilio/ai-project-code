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

    /// <summary>
    /// Gets the <see cref="KestrelServerOptions"/>.
    /// </summary>
    public KestrelServerOptions Options => _innerKestrelServer.Options;

    /// <inheritdoc />
    public Task StartAsync<TContext>(IHttpApplication<TContext> application, CancellationToken cancellationToken) where TContext : notnull
    {
        return _innerKestrelServer.StartAsync(application, cancellationToken);
    }

    // Graceful shutdown if possible
    /// <inheritdoc />
    public Task StopAsync(CancellationToken cancellationToken)
    {
        return _innerKestrelServer.StopAsync(cancellationToken);
    }

    // Ungraceful shutdown
    /// <inheritdoc />
    public void Dispose()
    {
        _innerKestrelServer.Dispose();
    }