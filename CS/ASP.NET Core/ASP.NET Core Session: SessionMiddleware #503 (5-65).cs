using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.DataProtection;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.Session;

/// <summary>
/// Enables the session state for the application.
/// </summary>
public class SessionMiddleware
{
    private const int SessionKeyLength = 36; // "382c74c3-721d-4f34-80e5-57657b6cbc27"
    private static readonly Func<bool> ReturnTrue = () => true;
    private readonly RequestDelegate _next;
    private readonly SessionOptions _options;
    private readonly ILogger _logger;
    private readonly ISessionStore _sessionStore;
    private readonly IDataProtector _dataProtector;

    /// <summary>
    /// Creates a new <see cref="SessionMiddleware"/>.
    /// </summary>
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