/// <summary>
/// A helper class for constructing encoded Uris for use in headers and other Uris.
/// </summary>
public static class UriHelper
{
    private const char ForwardSlash = '/';
    private const char Hash = '#';
    private const char QuestionMark = '?';
    private static readonly string SchemeDelimiter = Uri.SchemeDelimiter;
    private static readonly SpanAction<char, (string scheme, string host, string pathBase, string path, string query, string fragment)> InitializeAbsoluteUriStringSpanAction = new(InitializeAbsoluteUriString);

    /// <summary>
    /// Combines the given URI components into a string that is properly encoded for use in HTTP headers.
    /// </summary>
    /// <param name="pathBase">The first portion of the request path associated with application root.</param>
    /// <param name="path">The portion of the request path that identifies the requested resource.</param>
    /// <param name="query">The query, if any.</param>
    /// <param name="fragment">The fragment, if any.</param>
    /// <returns>The combined URI components, properly encoded for use in HTTP headers.</returns>
    public static string BuildRelative(
        PathString pathBase = new PathString(),
        PathString path = new PathString(),
        QueryString query = new QueryString(),
        FragmentString fragment = new FragmentString())
    {
        string combinePath = (pathBase.HasValue || path.HasValue) ? (pathBase + path).ToString() : "/";
        return combinePath + query.ToString() + fragment.ToString();
    }

    /// <summary>
    /// Combines the given URI components into a string that is properly encoded for use in HTTP headers.
    /// Note that unicode in the HostString will be encoded as punycode.
    /// </summary>
    /// <param name="scheme">http, https, etc.</param>
    /// <param name="host">The host portion of the uri normally included in the Host header. This may include the port.</param>
    /// <param name="pathBase">The first portion of the request path associated with application root.</param>
    /// <param name="path">The portion of the request path that identifies the requested resource.</param>
    /// <param name="query">The query, if any.</param>
    /// <param name="fragment">The fragment, if any.</param>
    /// <returns>The combined URI components, properly encoded for use in HTTP headers.</returns>
    public static string BuildAbsolute(