/// </summary>
public class QueryStringRequestCultureProvider : RequestCultureProvider
{
    /// <summary>
    /// The key that contains the culture name.
    /// Defaults to "culture".
    /// </summary>
    public string QueryStringKey { get; set; } = "culture";

    /// <summary>
    /// The key that contains the UI culture name. If not specified or no value is found,
    /// <see cref="QueryStringKey"/> will be used.
    /// Defaults to "ui-culture".
    /// </summary>
    public string UIQueryStringKey { get; set; } = "ui-culture";

    /// <inheritdoc />
    public override Task<ProviderCultureResult?> DetermineProviderCultureResult(HttpContext httpContext)
    {
        ArgumentNullException.ThrowIfNull(httpContext);

        var request = httpContext.Request;
        if (!request.QueryString.HasValue)
        {
            return NullProviderCultureResult;
        }

        string? queryCulture = null;
        string? queryUICulture = null;

        if (!string.IsNullOrWhiteSpace(QueryStringKey))
        {
            queryCulture = request.Query[QueryStringKey];
        }

        if (!string.IsNullOrWhiteSpace(UIQueryStringKey))
        {
            queryUICulture = request.Query[UIQueryStringKey];
        }

        if (queryCulture == null && queryUICulture == null)