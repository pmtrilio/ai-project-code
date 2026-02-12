using System.IO.Pipelines;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.AspNetCore.Routing;
using Microsoft.AspNetCore.Shared;

namespace Microsoft.AspNetCore.Http;

/// <summary>
/// Represents the incoming side of an individual HTTP request.
/// </summary>
[DebuggerDisplay("{DebuggerToString(),nq}")]
[DebuggerTypeProxy(typeof(HttpRequestDebugView))]
public abstract class HttpRequest
{
    /// <summary>
    /// Gets the <see cref="HttpContext"/> for this request.
    /// </summary>
    public abstract HttpContext HttpContext { get; }

    /// <summary>
    /// Gets or sets the HTTP method.
    /// </summary>
    /// <returns>The HTTP method.</returns>
    public abstract string Method { get; set; }

    /// <summary>
    /// Gets or sets the HTTP request scheme.
    /// </summary>
    /// <returns>The HTTP request scheme.</returns>
    public abstract string Scheme { get; set; }

    /// <summary>
    /// Returns true if the RequestScheme is https.
    /// </summary>
    /// <returns>true if this request is using https; otherwise, false.</returns>
    public abstract bool IsHttps { get; set; }

    /// <summary>
    /// Gets or sets the Host header. May include the port.
    /// </summary>
    /// <return>The Host header.</return>
    public abstract HostString Host { get; set; }

    /// <summary>
    /// Gets or sets the base path for the request. The path base should not end with a trailing slash.
    /// </summary>
    /// <returns>The base path for the request.</returns>
    public abstract PathString PathBase { get; set; }

    /// <summary>
    /// Gets or sets the portion of the request path that identifies the requested resource.