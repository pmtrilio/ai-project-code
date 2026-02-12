// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Extensions;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.AspNetCore.Rewrite.Logging;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.Rewrite;

/// <summary>
/// Represents a middleware that rewrites urls
/// </summary>
public class RewriteMiddleware
{
    private readonly RequestDelegate _next;
    private readonly RewriteOptions _options;
    private readonly IFileProvider _fileProvider;
    private readonly ILogger _logger;

    /// <summary>
    /// Creates a new instance of <see cref="RewriteMiddleware"/>
    /// </summary>
    /// <param name="next">The delegate representing the next middleware in the request pipeline.</param>
    /// <param name="hostingEnvironment">The Hosting Environment.</param>
    /// <param name="loggerFactory">The Logger Factory.</param>
    /// <param name="options">The middleware options, containing the rules to apply.</param>
    public RewriteMiddleware(
        RequestDelegate next,
        IWebHostEnvironment hostingEnvironment,
        ILoggerFactory loggerFactory,
        IOptions<RewriteOptions> options)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(options);

        _next = next;