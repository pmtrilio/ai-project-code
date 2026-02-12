
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
        // There's already an endpoint, skip matching completely
        var endpoint = httpContext.GetEndpoint();
        if (endpoint != null)
        {
            Log.MatchSkipped(_logger, endpoint);
            return _next(httpContext);
        }

        // There's an inherent race condition between waiting for init and accessing the matcher
        // this is OK because once `_matcher` is initialized, it will not be set to null again.
        var matcherTask = InitializeAsync();
        if (!matcherTask.IsCompletedSuccessfully)
        {
            return AwaitMatcher(this, httpContext, matcherTask);
        }

        var matchTask = matcherTask.Result.MatchAsync(httpContext);
        if (!matchTask.IsCompletedSuccessfully)
        {
            return AwaitMatch(this, httpContext, matchTask);
        }

        return SetRoutingAndContinue(httpContext);

        // Awaited fallbacks for when the Tasks do not synchronously complete
        static async Task AwaitMatcher(EndpointRoutingMiddleware middleware, HttpContext httpContext, Task<Matcher> matcherTask)
        {
            var matcher = await matcherTask;
            await matcher.MatchAsync(httpContext);
            await middleware.SetRoutingAndContinue(httpContext);
        }

        static async Task AwaitMatch(EndpointRoutingMiddleware middleware, HttpContext httpContext, Task matchTask)
        {
            await matchTask;
            await middleware.SetRoutingAndContinue(httpContext);
        }
    }

    [MethodImpl(MethodImplOptions.AggressiveInlining)]