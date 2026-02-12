
    // see https://tools.ietf.org/html/rfc7232#section-4.1
    private static readonly string[] HeadersToIncludeIn304 =
        new[] { "Cache-Control", "Content-Location", "Date", "ETag", "Expires", "Vary" };

    private readonly RequestDelegate _next;
    private readonly ResponseCachingOptions _options;
    private readonly ILogger _logger;
    private readonly IResponseCachingPolicyProvider _policyProvider;
    private readonly IResponseCache _cache;
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
        {
            // Can this request be served from cache?
            if (_policyProvider.AllowCacheLookup(context) && await TryServeFromCacheAsync(context))
            {
                return;
            }

            // Should we store the response to this request?
            if (_policyProvider.AllowCacheStorage(context))
            {
                // Hook up to listen to the response stream
                ShimResponseStream(context);

                try
                {
                    await _next(httpContext);

                    // If there was no response body, check the response headers now. We can cache things like redirects.
                    StartResponse(context);

                    // Finalize the cache entry
                    FinalizeCacheBody(context);
                }
                finally
                {
                    UnshimResponseStream(context);
                }

                return;
            }
        }

        // Response should not be captured but add IResponseCachingFeature which may be required when the response is generated
        AddResponseCachingFeature(httpContext);

        try
        {
            await _next(httpContext);
        }
        finally
        {
            RemoveResponseCachingFeature(httpContext);
        }
    }

    internal async Task<bool> TryServeCachedResponseAsync(ResponseCachingContext context, IResponseCacheEntry? cacheEntry)
    {
        if (!(cacheEntry is CachedResponse cachedResponse))
        {
            return false;