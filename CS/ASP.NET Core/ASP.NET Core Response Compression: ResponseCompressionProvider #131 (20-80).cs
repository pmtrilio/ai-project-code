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