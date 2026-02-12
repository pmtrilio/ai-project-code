/// </summary>
public class CookieRequestCultureProvider : RequestCultureProvider
{
    private const char _cookieSeparator = '|';
    private const string _culturePrefix = "c=";
    private const string _uiCulturePrefix = "uic=";

    /// <summary>
    /// Represent the default cookie name used to track the user's preferred culture information, which is ".AspNetCore.Culture".
    /// </summary>
    public static readonly string DefaultCookieName = ".AspNetCore.Culture";

    /// <summary>
    /// The name of the cookie that contains the user's preferred culture information.
    /// Defaults to <see cref="DefaultCookieName"/>.
    /// </summary>
    public string CookieName { get; set; } = DefaultCookieName;

    /// <inheritdoc />
    public override Task<ProviderCultureResult?> DetermineProviderCultureResult(HttpContext httpContext)
    {
        ArgumentNullException.ThrowIfNull(httpContext);

        var cookie = httpContext.Request.Cookies[CookieName];

        if (string.IsNullOrEmpty(cookie))
        {
            return NullProviderCultureResult;
        }

        var providerResultCulture = ParseCookieValue(cookie);

        return Task.FromResult<ProviderCultureResult?>(providerResultCulture);
    }

    /// <summary>
    /// Creates a string representation of a <see cref="RequestCulture"/> for placement in a cookie.
    /// </summary>
    /// <param name="requestCulture">The <see cref="RequestCulture"/>.</param>
    /// <returns>The cookie value.</returns>
    public static string MakeCookieValue(RequestCulture requestCulture)