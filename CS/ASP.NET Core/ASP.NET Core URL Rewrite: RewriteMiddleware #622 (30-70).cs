    /// <param name="loggerFactory">The Logger Factory.</param>
    /// <param name="options">The middleware options, containing the rules to apply.</param>
    public RewriteMiddleware(
        RequestDelegate next,
        IWebHostEnvironment hostingEnvironment,
        ILoggerFactory loggerFactory,
        IOptions<RewriteOptions> options)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(options);

        _next = next;
        _options = options.Value;
        _fileProvider = _options.StaticFileProvider ?? hostingEnvironment.WebRootFileProvider;
        _logger = loggerFactory.CreateLogger<RewriteMiddleware>();
    }

    /// <summary>
    /// Executes the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> for the current request.</param>
    /// <returns>A task that represents the execution of this middleware.</returns>
    public Task Invoke(HttpContext context)
    {
        ArgumentNullException.ThrowIfNull(context);

        var rewriteContext = new RewriteContext
        {
            HttpContext = context,
            StaticFileProvider = _fileProvider,
            Logger = _logger,
            Result = RuleResult.ContinueRules
        };

        var originalPath = context.Request.Path;

        RunRules(rewriteContext, _options, context, _logger);
        if (rewriteContext.Result == RuleResult.EndResponse)
        {
            return Task.CompletedTask;
        }