// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Caching.Memory;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.ObjectPool;
using Microsoft.Extensions.Options;
using Microsoft.Extensions.Primitives;
using Microsoft.Net.Http.Headers;

namespace Microsoft.AspNetCore.ResponseCaching;

/// <summary>
/// Enable HTTP response caching.
/// </summary>
public class ResponseCachingMiddleware
{
    private static readonly TimeSpan DefaultExpirationTimeSpan = TimeSpan.FromSeconds(10);

    // see https://tools.ietf.org/html/rfc7232#section-4.1
    private static readonly string[] HeadersToIncludeIn304 =
        new[] { "Cache-Control", "Content-Location", "Date", "ETag", "Expires", "Vary" };

    private readonly RequestDelegate _next;
    private readonly ResponseCachingOptions _options;
    private readonly ILogger _logger;
    private readonly IResponseCachingPolicyProvider _policyProvider;
    private readonly IResponseCache _cache;
    private readonly IResponseCachingKeyProvider _keyProvider;

    /// <summary>
    /// Creates a new <see cref="ResponseCachingMiddleware"/>.
    /// </summary>
    /// <param name="next">The <see cref="RequestDelegate"/> representing the next middleware in the pipeline.</param>
    /// <param name="options">The options for this middleware.</param>
    /// <param name="loggerFactory">The <see cref="ILoggerFactory"/> used for logging.</param>
    /// <param name="poolProvider">The <see cref="ObjectPoolProvider"/> used for creating <see cref="ObjectPool"/> instances.</param>
    public ResponseCachingMiddleware(
        RequestDelegate next,
        IOptions<ResponseCachingOptions> options,
        ILoggerFactory loggerFactory,
        ObjectPoolProvider poolProvider)
        : this(
            next,
            options,
            loggerFactory,
            new ResponseCachingPolicyProvider(),
            new MemoryResponseCache(new MemoryCache(new MemoryCacheOptions
            {
                SizeLimit = options.Value.SizeLimit
            })),
            new ResponseCachingKeyProvider(poolProvider, options))
    { }

    // for testing
    internal ResponseCachingMiddleware(
        RequestDelegate next,
        IOptions<ResponseCachingOptions> options,
        ILoggerFactory loggerFactory,
        IResponseCachingPolicyProvider policyProvider,