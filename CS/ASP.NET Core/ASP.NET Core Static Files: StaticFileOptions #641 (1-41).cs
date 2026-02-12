// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using Microsoft.AspNetCore.Http.Features;
using Microsoft.AspNetCore.StaticFiles;
using Microsoft.AspNetCore.StaticFiles.Infrastructure;

namespace Microsoft.AspNetCore.Builder;

/// <summary>
/// Options for serving static files
/// </summary>
public class StaticFileOptions : SharedOptionsBase
{
    internal static readonly Action<StaticFileResponseContext> _defaultOnPrepareResponse = _ => { };
    internal static readonly Func<StaticFileResponseContext, Task> _defaultOnPrepareResponseAsync = _ => Task.CompletedTask;

    /// <summary>
    /// Defaults to all request paths
    /// </summary>
    public StaticFileOptions() : this(new SharedOptions())
    {
    }

    /// <summary>
    /// Defaults to all request paths
    /// </summary>
    /// <param name="sharedOptions"></param>
    public StaticFileOptions(SharedOptions sharedOptions) : base(sharedOptions)
    {
        OnPrepareResponse = _defaultOnPrepareResponse;
        OnPrepareResponseAsync = _defaultOnPrepareResponseAsync;
    }

    /// <summary>
    /// Used to map files to content-types.
    /// </summary>
    public IContentTypeProvider ContentTypeProvider { get; set; } = default!;

    /// <summary>
    /// The default content type for a request if the ContentTypeProvider cannot determine one.