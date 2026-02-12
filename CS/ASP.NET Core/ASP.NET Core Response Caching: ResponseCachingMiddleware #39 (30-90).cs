    private readonly IResponseCachingKeyProvider _keyProvider;

    /// <summary>
    /// Creates a new <see cref="ResponseCachingMiddleware"/>.
    /// </summary>
    /// <param name="next">The <see cref="RequestDelegate"/> representing the next middleware in the pipeline.</param>
    /// <param name="options">The options for this middleware.</param>
    /// <param name="loggerFactory">The <see cref="ILoggerFactory"/> used for logging.</param>
    /// <param name="poolProvider">The <see cref="ObjectPoolProvider"/> used for creating <see cref="ObjectPool"/> instances.</param>
    public ResponseCachingMiddleware(
        RequestDelegate next,
        IOptions<ResponseCachingOptions> options,
        ILoggerFactory loggerFactory,
        ObjectPoolProvider poolProvider)
        : this(
            next,
            options,
            loggerFactory,
            new ResponseCachingPolicyProvider(),
            new MemoryResponseCache(new MemoryCache(new MemoryCacheOptions
            {
                SizeLimit = options.Value.SizeLimit
            })),
            new ResponseCachingKeyProvider(poolProvider, options))
    { }

    // for testing
    internal ResponseCachingMiddleware(
        RequestDelegate next,
        IOptions<ResponseCachingOptions> options,
        ILoggerFactory loggerFactory,
        IResponseCachingPolicyProvider policyProvider,
        IResponseCache cache,
        IResponseCachingKeyProvider keyProvider)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(options);
        ArgumentNullException.ThrowIfNull(loggerFactory);
        ArgumentNullException.ThrowIfNull(policyProvider);
        ArgumentNullException.ThrowIfNull(cache);
        ArgumentNullException.ThrowIfNull(keyProvider);

        _next = next;
        _options = options.Value;
        _logger = loggerFactory.CreateLogger<ResponseCachingMiddleware>();
        _policyProvider = policyProvider;
        _cache = cache;
        _keyProvider = keyProvider;
    }

    /// <summary>
    /// Invokes the logic of the middleware.
    /// </summary>
    /// <param name="httpContext">The <see cref="HttpContext"/>.</param>
    /// <returns>A <see cref="Task"/> that completes when the middleware has completed processing.</returns>
    public async Task Invoke(HttpContext httpContext)
    {
        var context = new ResponseCachingContext(httpContext, _logger);

        // Should we attempt any caching logic?
        if (_policyProvider.AttemptResponseCaching(context))