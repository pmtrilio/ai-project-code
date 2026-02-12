public interface IHttpRequestFeature
{
    /// <summary>
    /// Gets or set the HTTP-version as defined in RFC 7230. E.g. "HTTP/1.1"
    /// </summary>
    string Protocol { get; set; }

    /// <summary>
    /// Gets or set the request uri scheme. E.g. "http" or "https".
    /// <para>
    /// Note this value is not included in the original request,
    /// it is inferred by checking if the transport used a TLS
    /// connection or not.
    /// </para>
    /// </summary>
    string Scheme { get; set; }

    /// <summary>
    /// Gets or sets the request method as defined in RFC 7230. E.g. "GET", "HEAD", "POST", etc..
    /// </summary>
    string Method { get; set; }

    /// <summary>
    /// Gets or sets the first portion of the request path associated with application root.
    /// <para>
    /// The value is un-escaped. The value may be <see cref="string.Empty"/>.
    /// </para>
    /// </summary>
    string PathBase { get; set; }

    /// <summary>
    /// Gets or sets the portion of the request path that identifies the requested resource.
    /// <para>
    /// The value may be <see cref="string.Empty"/> if <see cref="PathBase"/> contains the full path,
    /// or for 'OPTIONS *' requests.
    /// The path is fully decoded by the server except for '%2F', which would decode to '/' and
    /// change the meaning of the path segments. '%2F' can only be replaced after splitting the path into segments.
    /// </para>
    /// </summary>
    string Path { get; set; }
