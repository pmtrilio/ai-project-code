
/// <summary>
/// Extension methods to expose Authentication on HttpContext.
/// </summary>
public static class AuthenticationHttpContextExtensions
{
    /// <summary>
    /// Authenticate the current request using the default authentication scheme.
    /// The default authentication scheme can be configured using <see cref="AuthenticationOptions.DefaultAuthenticateScheme"/>.
    /// </summary>
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