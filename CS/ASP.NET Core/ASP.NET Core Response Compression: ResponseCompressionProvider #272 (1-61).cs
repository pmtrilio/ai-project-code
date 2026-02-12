// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics;
using System.Linq;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Microsoft.Extensions.Primitives;
using Microsoft.Net.Http.Headers;

namespace Microsoft.AspNetCore.ResponseCompression;

/// <inheritdoc />
public class ResponseCompressionProvider : IResponseCompressionProvider
{
    private readonly ICompressionProvider[] _providers;
    private readonly HashSet<string> _mimeTypes;
    private readonly HashSet<string> _excludedMimeTypes;
    private readonly bool _enableForHttps;
    private readonly ILogger _logger;

    /// <summary>
    /// If no compression providers are specified then GZip is used by default.
    /// </summary>
    /// <param name="services">Services to use when instantiating compression providers.</param>
    /// <param name="options">The options for this instance.</param>
    public ResponseCompressionProvider(IServiceProvider services, IOptions<ResponseCompressionOptions> options)
    {
        ArgumentNullException.ThrowIfNull(services);
        ArgumentNullException.ThrowIfNull(options);

        var responseCompressionOptions = options.Value;

        _providers = responseCompressionOptions.Providers.ToArray();
        if (_providers.Length == 0)
        {
            // Use the factory so it can resolve IOptions<GzipCompressionProviderOptions> from DI.
            _providers = new ICompressionProvider[]
            {
                    new CompressionProviderFactory(typeof(BrotliCompressionProvider)),
                    new CompressionProviderFactory(typeof(GzipCompressionProvider)),
            };
        }
        for (var i = 0; i < _providers.Length; i++)
        {
            var factory = _providers[i] as CompressionProviderFactory;
            if (factory != null)
            {
                _providers[i] = factory.CreateInstance(services);
            }
        }

        var mimeTypes = responseCompressionOptions.MimeTypes;
        if (mimeTypes == null || !mimeTypes.Any())
        {
            mimeTypes = ResponseCompressionDefaults.MimeTypes;
        }
