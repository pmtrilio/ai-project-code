
    private readonly RequestDelegate _next;
    private readonly Lazy<int> _httpsPort;
    private readonly int _statusCode;

    private readonly IServerAddressesFeature? _serverAddressesFeature;
    private readonly IConfiguration _config;
    private readonly ILogger _logger;

    /// <summary>
    /// Initializes <see cref="HttpsRedirectionMiddleware" />.
    /// </summary>
    /// <param name="next"></param>
    /// <param name="options"></param>
    /// <param name="config"></param>
    /// <param name="loggerFactory"></param>
    public HttpsRedirectionMiddleware(RequestDelegate next, IOptions<HttpsRedirectionOptions> options, IConfiguration config, ILoggerFactory loggerFactory)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(options);
        ArgumentNullException.ThrowIfNull(config);

        _next = next;
        _config = config;

        var httpsRedirectionOptions = options.Value;
        if (httpsRedirectionOptions.HttpsPort.HasValue)
        {
            _httpsPort = new Lazy<int>(httpsRedirectionOptions.HttpsPort.Value);
        }
        else
        {
            _httpsPort = new Lazy<int>(TryGetHttpsPort);
        }
        _statusCode = httpsRedirectionOptions.RedirectStatusCode;
        _logger = loggerFactory.CreateLogger<HttpsRedirectionMiddleware>();
    }

    /// <summary>
    /// Initializes <see cref="HttpsRedirectionMiddleware" />.
    /// </summary>
    /// <param name="next"></param>
    /// <param name="options"></param>
    /// <param name="config"></param>
    /// <param name="loggerFactory"></param>
    /// <param name="serverAddressesFeature"></param>
    public HttpsRedirectionMiddleware(RequestDelegate next, IOptions<HttpsRedirectionOptions> options, IConfiguration config, ILoggerFactory loggerFactory,
        IServerAddressesFeature serverAddressesFeature)
        : this(next, options, config, loggerFactory)
    {
        _serverAddressesFeature = serverAddressesFeature ?? throw new ArgumentNullException(nameof(serverAddressesFeature));
    }

    /// <summary>
    /// Invokes the HttpsRedirectionMiddleware.
    /// </summary>
    /// <param name="context"></param>
    /// <returns></returns>
    public Task Invoke(HttpContext context)
    {
        if (context.Request.IsHttps)