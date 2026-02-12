    /// <param name="context">The <see cref="HttpContext"/> context.</param>
    /// <returns>The <see cref="AuthenticateResult"/>.</returns>
    public static Task<AuthenticateResult> AuthenticateAsync(this HttpContext context) =>
        context.AuthenticateAsync(scheme: null);

    /// <summary>
    /// Authenticate the current request using the specified scheme.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> context.</param>
    /// <param name="scheme">The name of the authentication scheme.</param>
    /// <returns>The <see cref="AuthenticateResult"/>.</returns>
    public static Task<AuthenticateResult> AuthenticateAsync(this HttpContext context, string? scheme) =>
        GetAuthenticationService(context).AuthenticateAsync(context, scheme);

    /// <summary>
    /// Challenge the current request using the specified scheme.
    /// An authentication challenge can be issued when an unauthenticated user requests an endpoint that requires authentication.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> context.</param>
    /// <param name="scheme">The name of the authentication scheme.</param>
    /// <returns>The result.</returns>
    public static Task ChallengeAsync(this HttpContext context, string? scheme) =>
        context.ChallengeAsync(scheme, properties: null);

    /// <summary>
    /// Challenge the current request using the default challenge scheme.
    /// An authentication challenge can be issued when an unauthenticated user requests an endpoint that requires authentication.
    /// The default challenge scheme can be configured using <see cref="AuthenticationOptions.DefaultChallengeScheme"/>.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> context.</param>
    /// <returns>The task.</returns>
    public static Task ChallengeAsync(this HttpContext context) =>
        context.ChallengeAsync(scheme: null, properties: null);

    /// <summary>
    /// Challenge the current request using the default challenge scheme.
    /// An authentication challenge can be issued when an unauthenticated user requests an endpoint that requires authentication.
    /// The default challenge scheme can be configured using <see cref="AuthenticationOptions.DefaultChallengeScheme"/>.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> context.</param>
    /// <param name="properties">The <see cref="AuthenticationProperties"/> properties.</param>
    /// <returns>The task.</returns>
    public static Task ChallengeAsync(this HttpContext context, AuthenticationProperties? properties) =>
        context.ChallengeAsync(scheme: null, properties: properties);

    /// <summary>
    /// Challenge the current request using the specified scheme.
    /// An authentication challenge can be issued when an unauthenticated user requests an endpoint that requires authentication.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> context.</param>
    /// <param name="scheme">The name of the authentication scheme.</param>