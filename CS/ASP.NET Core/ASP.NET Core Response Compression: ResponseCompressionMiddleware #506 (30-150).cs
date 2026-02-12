    }

    /// <summary>
    /// Invoke the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/>.</param>
    /// <returns>A task that represents the execution of this middleware.</returns>
    public Task Invoke(HttpContext context)
    {
        if (!_provider.CheckRequestAcceptsCompression(context))
        {
            return _next(context);
        }
        return InvokeCore(context);
    }

    private async Task InvokeCore(HttpContext context)
    {
        var originalBodyFeature = context.Features.Get<IHttpResponseBodyFeature>();
        var originalCompressionFeature = context.Features.Get<IHttpsCompressionFeature>();

        Debug.Assert(originalBodyFeature != null);

        var compressionBody = new ResponseCompressionBody(context, _provider, originalBodyFeature);
        context.Features.Set<IHttpResponseBodyFeature>(compressionBody);
        context.Features.Set<IHttpsCompressionFeature>(compressionBody);

        try
        {
            await _next(context);
            await compressionBody.FinishCompressionAsync();
        }
        finally
        {
            context.Features.Set(originalBodyFeature);
            context.Features.Set(originalCompressionFeature);
        }
    }
}
