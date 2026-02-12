    /// <param name="next">The <see cref="RequestDelegate"/> representing the next middleware in the pipeline.</param>
    /// <param name="loggerFactory">The <see cref="ILoggerFactory"/> representing the factory that used to create logger instances.</param>
    /// <param name="dataProtectionProvider">The <see cref="IDataProtectionProvider"/> used to protect and verify the cookie.</param>
    /// <param name="sessionStore">The <see cref="ISessionStore"/> representing the session store.</param>
    /// <param name="options">The session configuration options.</param>
    public SessionMiddleware(
        RequestDelegate next,
        ILoggerFactory loggerFactory,
        IDataProtectionProvider dataProtectionProvider,
        ISessionStore sessionStore,
        IOptions<SessionOptions> options)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(loggerFactory);
        ArgumentNullException.ThrowIfNull(dataProtectionProvider);
        ArgumentNullException.ThrowIfNull(sessionStore);
        ArgumentNullException.ThrowIfNull(options);

        _next = next;
        _logger = loggerFactory.CreateLogger<SessionMiddleware>();
        _dataProtector = dataProtectionProvider.CreateProtector(nameof(SessionMiddleware));
        _options = options.Value;
        _sessionStore = sessionStore;
    }

    /// <summary>
    /// Invokes the logic of the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/>.</param>
    /// <returns>A <see cref="Task"/> that completes when the middleware has completed processing.</returns>
    public async Task Invoke(HttpContext context)
    {
        var isNewSessionKey = false;
        Func<bool> tryEstablishSession = ReturnTrue;
        var cookieValue = context.Request.Cookies[_options.Cookie.Name!];
        var sessionKey = CookieProtection.Unprotect(_dataProtector, cookieValue, _logger);
        if (string.IsNullOrWhiteSpace(sessionKey) || sessionKey.Length != SessionKeyLength)
        {
            // No valid cookie, new session.
            sessionKey = GetSessionKey();

            static string GetSessionKey()
            {
                Span<byte> guidBytes = stackalloc byte[16];
                RandomNumberGenerator.Fill(guidBytes);
                return new Guid(guidBytes).ToString();
            }

            cookieValue = CookieProtection.Protect(_dataProtector, sessionKey);
            var establisher = new SessionEstablisher(context, cookieValue, _options);
            tryEstablishSession = establisher.TryEstablishSession;
            isNewSessionKey = true;
        }

        var feature = new SessionFeature();
        feature.Session = _sessionStore.Create(sessionKey, _options.IdleTimeout, _options.IOTimeout, tryEstablishSession, isNewSessionKey);
        context.Features.Set<ISessionFeature>(feature);

        try
        {
            await _next(context);