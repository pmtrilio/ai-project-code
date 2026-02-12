// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics;
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
    /// <para>
    /// The value may be <see cref="PathString.Empty"/> if <see cref="PathBase"/> contains the full path,
    /// or for 'OPTIONS *' requests.
    /// The path is fully decoded by the server except for '%2F', which would decode to '/' and
    /// change the meaning of the path segments. '%2F' can only be replaced after splitting the path into segments.
    /// </para>
    /// </summary>
    public abstract PathString Path { get; set; }

    /// <summary>
    /// Gets or sets the raw query string used to create the query collection in Request.Query.
    /// </summary>
    /// <returns>The raw query string.</returns>
    public abstract QueryString QueryString { get; set; }

    /// <summary>
    /// Gets the query value collection parsed from Request.QueryString.
    /// </summary>
    /// <returns>The query value collection parsed from Request.QueryString.</returns>
    public abstract IQueryCollection Query { get; set; }

    /// <summary>
    /// Gets or sets the request protocol (e.g. HTTP/1.1).
    /// </summary>
    /// <returns>The request protocol.</returns>
    public abstract string Protocol { get; set; }