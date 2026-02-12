        RequestDelegate next,
        IOptions<DeveloperExceptionPageOptions> options,
        ILoggerFactory loggerFactory,
        IWebHostEnvironment hostingEnvironment,
        DiagnosticSource diagnosticSource,
        IEnumerable<IDeveloperPageExceptionFilter> filters)
    {
        _innerMiddlewareImpl = new(
            next,
            options,
            loggerFactory,
            hostingEnvironment,
            diagnosticSource,
            filters,
            new DummyMeterFactory(),
            problemDetailsService: null);
    }

    /// <summary>
    /// Process an individual request.
    /// </summary>
    /// <param name="context"></param>
    /// <returns></returns>
    public Task Invoke(HttpContext context)
        => _innerMiddlewareImpl.Invoke(context);
}
