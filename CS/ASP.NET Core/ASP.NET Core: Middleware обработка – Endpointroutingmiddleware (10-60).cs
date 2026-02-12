using Microsoft.AspNetCore.Cors.Infrastructure;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Features;
using Microsoft.AspNetCore.Http.Metadata;
using Microsoft.AspNetCore.Routing.Matching;
using Microsoft.AspNetCore.Routing.ShortCircuit;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.Routing;

internal sealed partial class EndpointRoutingMiddleware
{
    private const string DiagnosticsEndpointMatchedKey = "Microsoft.AspNetCore.Routing.EndpointMatched";

    private readonly MatcherFactory _matcherFactory;
    private readonly ILogger _logger;
    private readonly EndpointDataSource _endpointDataSource;
    private readonly DiagnosticListener _diagnosticListener;
    private readonly RoutingMetrics _metrics;
    private readonly RequestDelegate _next;
    private readonly RouteOptions _routeOptions;
    private Task<Matcher>? _initializationTask;

    public EndpointRoutingMiddleware(
        MatcherFactory matcherFactory,
        ILogger<EndpointRoutingMiddleware> logger,
        IEndpointRouteBuilder endpointRouteBuilder,
        EndpointDataSource rootCompositeEndpointDataSource,
        DiagnosticListener diagnosticListener,
        IOptions<RouteOptions> routeOptions,
        RoutingMetrics metrics,
        RequestDelegate next)
    {
        ArgumentNullException.ThrowIfNull(endpointRouteBuilder);

        _matcherFactory = matcherFactory ?? throw new ArgumentNullException(nameof(matcherFactory));
        _logger = logger ?? throw new ArgumentNullException(nameof(logger));
        _diagnosticListener = diagnosticListener ?? throw new ArgumentNullException(nameof(diagnosticListener));
        _metrics = metrics;
        _next = next ?? throw new ArgumentNullException(nameof(next));
        _routeOptions = routeOptions.Value;

        // rootCompositeEndpointDataSource is a constructor parameter only so it always gets disposed by DI. This ensures that any
        // disposable EndpointDataSources also get disposed. _endpointDataSource is a component of rootCompositeEndpointDataSource.
        _ = rootCompositeEndpointDataSource;
        _endpointDataSource = new CompositeEndpointDataSource(endpointRouteBuilder.DataSources);
    }

    public Task Invoke(HttpContext httpContext)
    {