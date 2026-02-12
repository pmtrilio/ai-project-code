// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.IO.Pipelines;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.AspNetCore.Shared;

namespace Microsoft.AspNetCore.Http;

/// <summary>
/// Represents the outgoing side of an individual HTTP request.
/// </summary>
[DebuggerDisplay("{DebuggerToString(),nq}")]
[DebuggerTypeProxy(typeof(HttpResponseDebugView))]
public abstract class HttpResponse
{
    private static readonly Func<object, Task> _callbackDelegate = callback => ((Func<Task>)callback)();
    private static readonly Func<object, Task> _disposeDelegate = state =>
    {
        // Prefer async dispose over dispose
        if (state is IAsyncDisposable asyncDisposable)
        {
            return asyncDisposable.DisposeAsync().AsTask();
        }
        else if (state is IDisposable disposable)
        {
            disposable.Dispose();
        }
        return Task.CompletedTask;
    };

    /// <summary>
    /// Gets the <see cref="HttpContext"/> for this response.
    /// </summary>
    public abstract HttpContext HttpContext { get; }

    /// <summary>
    /// Gets or sets the HTTP response code.
    /// </summary>
    public abstract int StatusCode { get; set; }

    /// <summary>
    /// Gets the response headers.
    /// </summary>
    public abstract IHeaderDictionary Headers { get; }

    /// <summary>
    /// Gets or sets the response body <see cref="Stream"/>.
    /// </summary>
    public abstract Stream Body { get; set; }

    /// <summary>
    /// Gets the response body <see cref="PipeWriter"/>
    /// </summary>
    /// <value>The response body <see cref="PipeWriter"/>.</value>
    public virtual PipeWriter BodyWriter { get => throw new NotImplementedException(); }

    /// <summary>
    /// Gets or sets the value for the <c>Content-Length</c> response header.