    /// <summary>
    /// The current options for the <see cref="RequestLocalizationMiddleware"/>.
    /// </summary>
    public RequestLocalizationOptions? Options { get; set; }

    /// <inheritdoc />
    public abstract Task<ProviderCultureResult?> DetermineProviderCultureResult(HttpContext httpContext);
}
