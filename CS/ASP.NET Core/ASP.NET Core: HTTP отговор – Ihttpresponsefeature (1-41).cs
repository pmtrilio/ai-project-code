// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

namespace Microsoft.AspNetCore.Http.Features;

/// <summary>
/// Represents the fields and state of an HTTP response.
/// </summary>
public interface IHttpResponseFeature
{
    /// <summary>
    /// Gets or sets the status-code as defined in RFC 7230.
    /// </summary>
    /// <value>Defaults to <c>200</c>.</value>
    int StatusCode { get; set; }

    /// <summary>
    /// Gets or sets the reason-phrase as defined in RFC 7230. Note this field is no longer supported by HTTP/2.
    /// </summary>
    string? ReasonPhrase { get; set; }

    /// <summary>
    /// Gets or sets the response headers to send. Headers with multiple values will be emitted as multiple headers.
    /// </summary>
    IHeaderDictionary Headers { get; set; }

    /// <summary>
    /// Gets or sets the <see cref="Stream"/> for writing the response body.
    /// </summary>
    [Obsolete("Use IHttpResponseBodyFeature.Stream instead.", error: false)]
    Stream Body { get; set; }

    /// <summary>
    /// Gets a value that indicates if the response has started.
    /// <para>
    /// If <see langword="true"/>, the <see cref="StatusCode"/>,
    /// <see cref="ReasonPhrase"/>, and <see cref="Headers"/> are now immutable, and
    /// <see cref="OnStarting(Func{object, Task}, object)"/> should no longer be called.
    /// </para>
    /// </summary>
    bool HasStarted { get; }