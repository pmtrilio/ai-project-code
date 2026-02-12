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

        _mimeTypes = new HashSet<string>(mimeTypes, StringComparer.OrdinalIgnoreCase);

        _excludedMimeTypes = new HashSet<string>(
            responseCompressionOptions.ExcludedMimeTypes ?? Enumerable.Empty<string>(),
            StringComparer.OrdinalIgnoreCase
        );

        _enableForHttps = responseCompressionOptions.EnableForHttps;

        _logger = services.GetRequiredService<ILogger<ResponseCompressionProvider>>();
    }

    /// <inheritdoc />
    public virtual ICompressionProvider? GetCompressionProvider(HttpContext context)
    {
        // e.g. Accept-Encoding: gzip, deflate, sdch
        var accept = context.Request.Headers.AcceptEncoding;

        // Note this is already checked in CheckRequestAcceptsCompression which _should_ prevent any of these other methods from being called.
        if (StringValues.IsNullOrEmpty(accept))
        {
            Debug.Assert(false, "Duplicate check failed.");
            _logger.NoAcceptEncoding();
            return null;
        }

        if (!StringWithQualityHeaderValue.TryParseList(accept, out var encodings) || encodings.Count == 0)
        {
            _logger.NoAcceptEncoding();
            return null;
        }

        var candidates = new HashSet<ProviderCandidate>();

        foreach (var encoding in encodings)
        {
            var encodingName = encoding.Value;
            var quality = encoding.Quality.GetValueOrDefault(1);

            if (quality < double.Epsilon)
            {
                continue;
            }

            for (int i = 0; i < _providers.Length; i++)
            {
                var provider = _providers[i];

                if (StringSegment.Equals(provider.EncodingName, encodingName, StringComparison.OrdinalIgnoreCase))