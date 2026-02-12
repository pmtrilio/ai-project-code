// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Text.Encodings.Web;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.StaticFiles;

/// <summary>
/// Enables directory browsing
/// </summary>
public class DirectoryBrowserMiddleware
{
    private readonly DirectoryBrowserOptions _options;
    private readonly PathString _matchUrl;
    private readonly RequestDelegate _next;
    private readonly IDirectoryFormatter _formatter;
    private readonly IFileProvider _fileProvider;

    /// <summary>
    /// Creates a new instance of the SendFileMiddleware. Using <see cref="HtmlEncoder.Default"/> instance.
    /// </summary>
    /// <param name="next">The next middleware in the pipeline.</param>
    /// <param name="hostingEnv">The <see cref="IWebHostEnvironment"/> used by this middleware.</param>
    /// <param name="options">The configuration for this middleware.</param>
    public DirectoryBrowserMiddleware(RequestDelegate next, IWebHostEnvironment hostingEnv, IOptions<DirectoryBrowserOptions> options)
        : this(next, hostingEnv, HtmlEncoder.Default, options)
    {
    }

    /// <summary>
    /// Creates a new instance of the SendFileMiddleware.
    /// </summary>
    /// <param name="next">The next middleware in the pipeline.</param>
    /// <param name="hostingEnv">The <see cref="IWebHostEnvironment"/> used by this middleware.</param>
    /// <param name="encoder">The <see cref="HtmlEncoder"/> used by the default <see cref="HtmlDirectoryFormatter"/>.</param>
    /// <param name="options">The configuration for this middleware.</param>