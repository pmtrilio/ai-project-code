using System.Linq;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;
using Microsoft.Extensions.Primitives;

namespace Microsoft.AspNetCore.Localization;

/// <summary>
/// Enables automatic setting of the culture for <see cref="HttpRequest"/>s based on information
/// sent by the client in headers and logic provided by the application.
/// </summary>
public class RequestLocalizationMiddleware
{
    private const int MaxCultureFallbackDepth = 5;

    private readonly RequestDelegate _next;
    private readonly RequestLocalizationOptions _options;
    private readonly ILogger _logger;

    /// <summary>
    /// Creates a new <see cref="RequestLocalizationMiddleware"/>.
    /// </summary>
    /// <param name="next">The <see cref="RequestDelegate"/> representing the next middleware in the pipeline.</param>
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