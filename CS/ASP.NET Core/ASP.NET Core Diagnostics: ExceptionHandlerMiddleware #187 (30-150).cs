        IOptions<ExceptionHandlerOptions> options,
        DiagnosticListener diagnosticListener)
    {
        _innerMiddlewareImpl = new(
            next,
            loggerFactory,
            options,
            diagnosticListener,
            Enumerable.Empty<IExceptionHandler>(),
            new DummyMeterFactory(),
            problemDetailsService: null);
    }

    /// <summary>
    /// Executes the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> for the current request.</param>
    public Task Invoke(HttpContext context)
        => _innerMiddlewareImpl.Invoke(context);
}
