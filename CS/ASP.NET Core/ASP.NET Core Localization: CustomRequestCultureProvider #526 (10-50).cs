/// </summary>
public class CustomRequestCultureProvider : RequestCultureProvider
{
    private readonly Func<HttpContext, Task<ProviderCultureResult?>> _provider;

    /// <summary>
    /// Creates a new <see cref="CustomRequestCultureProvider"/> using the specified delegate.
    /// </summary>
    /// <param name="provider">The provider delegate.</param>
    public CustomRequestCultureProvider(Func<HttpContext, Task<ProviderCultureResult?>> provider)
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
