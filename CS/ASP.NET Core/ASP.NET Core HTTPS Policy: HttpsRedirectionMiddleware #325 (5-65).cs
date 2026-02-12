using Microsoft.AspNetCore.Hosting.Server.Features;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Extensions;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.HttpsPolicy;

/// <summary>
/// Middleware that redirects non-HTTPS requests to an HTTPS URL.
/// </summary>
public class HttpsRedirectionMiddleware
{
    private const int PortNotFound = -1;

    private readonly RequestDelegate _next;
    private readonly Lazy<int> _httpsPort;
    private readonly int _statusCode;

    private readonly IServerAddressesFeature? _serverAddressesFeature;
    private readonly IConfiguration _config;
    private readonly ILogger _logger;

    /// <summary>
    /// Initializes <see cref="HttpsRedirectionMiddleware" />.
    /// </summary>
    /// <param name="next"></param>
    /// <param name="options"></param>
    /// <param name="config"></param>
    /// <param name="loggerFactory"></param>
    public HttpsRedirectionMiddleware(RequestDelegate next, IOptions<HttpsRedirectionOptions> options, IConfiguration config, ILoggerFactory loggerFactory)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(options);
        ArgumentNullException.ThrowIfNull(config);

        _next = next;
        _config = config;

        var httpsRedirectionOptions = options.Value;
        if (httpsRedirectionOptions.HttpsPort.HasValue)
        {
            _httpsPort = new Lazy<int>(httpsRedirectionOptions.HttpsPort.Value);
        }
        else
        {
            _httpsPort = new Lazy<int>(TryGetHttpsPort);
        }
        _statusCode = httpsRedirectionOptions.RedirectStatusCode;
        _logger = loggerFactory.CreateLogger<HttpsRedirectionMiddleware>();
    }

    /// <summary>
    /// Initializes <see cref="HttpsRedirectionMiddleware" />.
    /// </summary>
    /// <param name="next"></param>
    /// <param name="options"></param>
    /// <param name="config"></param>
    /// <param name="loggerFactory"></param>
    /// <param name="serverAddressesFeature"></param>