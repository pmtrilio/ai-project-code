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

    /// <summary>
    /// Gets or sets the query portion of the request-target as defined in RFC 7230. The value
    /// may be <see cref="string.Empty" />. If not empty then the leading '?' will be included. The value
    /// is in its original form, without un-escaping.
    /// </summary>
    string QueryString { get; set; }

    /// <summary>
    /// Gets or sets the request target as it was sent in the HTTP request.
    /// <para>
    /// This property contains the raw path and full query, as well as other request targets
    /// such as * for OPTIONS requests (<see href="https://tools.ietf.org/html/rfc7230#section-5.3"/>).
    /// </para>
    /// </summary>
    /// <remarks>
    /// This property is not used internally for routing or authorization decisions. It has not
    /// been UrlDecoded and care should be taken in its use.
    /// </remarks>
    string RawTarget { get; set; }
