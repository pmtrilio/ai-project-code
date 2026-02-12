    {
        ArgumentNullException.ThrowIfNull(provider);

        _provider = provider;
    }

    /// <inheritdoc />
    public override Task<ProviderCultureResult?> DetermineProviderCultureResult(HttpContext httpContext)
    {
        ArgumentNullException.ThrowIfNull(httpContext);

        return _provider(httpContext);
    }
}
