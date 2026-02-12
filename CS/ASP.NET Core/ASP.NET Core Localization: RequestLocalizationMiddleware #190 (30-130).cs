    /// <param name="options">The <see cref="RequestLocalizationOptions"/> representing the options for the
    /// <see cref="RequestLocalizationMiddleware"/>.</param>
    /// <param name="loggerFactory">The <see cref="ILoggerFactory"/> used for logging.</param>
    public RequestLocalizationMiddleware(RequestDelegate next, IOptions<RequestLocalizationOptions> options, ILoggerFactory loggerFactory)
    {
        ArgumentNullException.ThrowIfNull(options);

        _next = next ?? throw new ArgumentNullException(nameof(next));
        _logger = loggerFactory?.CreateLogger<RequestLocalizationMiddleware>() ?? throw new ArgumentNullException(nameof(loggerFactory));
        _options = options.Value;
    }

    /// <summary>
    /// Invokes the logic of the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/>.</param>
    /// <returns>A <see cref="Task"/> that completes when the middleware has completed processing.</returns>
    public async Task Invoke(HttpContext context)
    {
        ArgumentNullException.ThrowIfNull(context);

        var requestCulture = _options.DefaultRequestCulture;

        IRequestCultureProvider? winningProvider = null;

        if (_options.RequestCultureProviders != null)
        {
            foreach (var provider in _options.RequestCultureProviders)
            {
                var providerResultCulture = await provider.DetermineProviderCultureResult(context);
                if (providerResultCulture == null)
                {
                    continue;
                }
                var cultures = providerResultCulture.Cultures;
                var uiCultures = providerResultCulture.UICultures;

                CultureInfo? cultureInfo = null;
                CultureInfo? uiCultureInfo = null;
                if (_options.SupportedCultures != null)
                {
                    cultureInfo = GetCultureInfo(
                        cultures,
                        _options.SupportedCultures,
                        _options.FallBackToParentCultures);

                    if (cultureInfo == null)
                    {
                        _logger.UnsupportedCultures(provider.GetType().Name, cultures);
                    }
                }

                if (_options.SupportedUICultures != null)
                {
                    uiCultureInfo = GetCultureInfo(
                        uiCultures,
                        _options.SupportedUICultures,
                        _options.FallBackToParentUICultures);

                    if (uiCultureInfo == null)
                    {
                        _logger.UnsupportedUICultures(provider.GetType().Name, uiCultures);
                    }
                }

                if (cultureInfo == null && uiCultureInfo == null)
                {
                    continue;
                }

                cultureInfo ??= _options.DefaultRequestCulture.Culture;
                uiCultureInfo ??= _options.DefaultRequestCulture.UICulture;

                var result = new RequestCulture(cultureInfo, uiCultureInfo);
                requestCulture = result;
                winningProvider = provider;
                break;
            }
        }

        context.Features.Set<IRequestCultureFeature>(new RequestCultureFeature(requestCulture, winningProvider));

        SetCurrentThreadCulture(requestCulture);

        if (_options.ApplyCurrentCultureToResponseHeaders)
        {
            var headers = context.Response.Headers;
            headers.ContentLanguage = requestCulture.UICulture.Name;
        }

        await _next(context);
    }

    private static void SetCurrentThreadCulture(RequestCulture requestCulture)
    {
        CultureInfo.CurrentCulture = requestCulture.Culture;
        CultureInfo.CurrentUICulture = requestCulture.UICulture;
    }

    private static CultureInfo? GetCultureInfo(
        IList<StringSegment> cultureNames,