
/// <summary>
/// Provides a collection of <see cref="Endpoint"/> instances.
/// </summary>
public abstract class EndpointDataSource
{
    /// <summary>
    /// Gets a <see cref="IChangeToken"/> used to signal invalidation of cached <see cref="Endpoint"/>
    /// instances.
    /// </summary>
    /// <returns>The <see cref="IChangeToken"/>.</returns>
    public abstract IChangeToken GetChangeToken();

    /// <summary>
    /// Returns a read-only collection of <see cref="Endpoint"/> instances.
    /// </summary>
    public abstract IReadOnlyList<Endpoint> Endpoints { get; }

    /// <summary>
    /// Get the <see cref="Endpoint"/> instances for this <see cref="EndpointDataSource"/> given the specified <see cref="RouteGroupContext.Prefix"/> and <see cref="RouteGroupContext.Conventions"/>.
    /// </summary>
    /// <param name="context">Details about how the returned <see cref="Endpoint"/> instances should be grouped and a reference to application services.</param>
    /// <returns>
    /// Returns a read-only collection of <see cref="Endpoint"/> instances given the specified group <see cref="RouteGroupContext.Prefix"/> and <see cref="RouteGroupContext.Conventions"/>.
    /// </returns>
    public virtual IReadOnlyList<Endpoint> GetGroupedEndpoints(RouteGroupContext context)
    {
        // Only evaluate Endpoints once per call.
        var endpoints = Endpoints;
        var wrappedEndpoints = new RouteEndpoint[endpoints.Count];

        for (int i = 0; i < endpoints.Count; i++)
        {
            var endpoint = endpoints[i];

            // Endpoint does not provide a RoutePattern but RouteEndpoint does. So it's impossible to apply a prefix for custom Endpoints.
            // Supporting arbitrary Endpoints just to add group metadata would require changing the Endpoint type breaking any real scenario.
            if (endpoint is not RouteEndpoint routeEndpoint)
            {
                throw new NotSupportedException(Resources.FormatMapGroup_CustomEndpointUnsupported(endpoint.GetType()));
            }

            // Make the full route pattern visible to IEndpointConventionBuilder extension methods called on the group.
            // This includes patterns from any parent groups.
            var fullRoutePattern = RoutePatternFactory.Combine(context.Prefix, routeEndpoint.RoutePattern);
            var routeEndpointBuilder = new RouteEndpointBuilder(routeEndpoint.RequestDelegate, fullRoutePattern, routeEndpoint.Order)
            {
                DisplayName = routeEndpoint.DisplayName,
                ApplicationServices = context.ApplicationServices,
            };

            // Apply group conventions to each endpoint in the group at a lower precedent than metadata already on the endpoint.
            foreach (var convention in context.Conventions)
            {
                convention(routeEndpointBuilder);
            }

            // Any metadata already on the RouteEndpoint must have been applied directly to the endpoint or to a nested group.
            // This makes the metadata more specific than what's being applied to this group. So add it after this group's conventions.
            foreach (var metadata in routeEndpoint.Metadata)
            {
                routeEndpointBuilder.Metadata.Add(metadata);
            }

            foreach (var finallyConvention in context.FinallyConventions)
            {
                finallyConvention(routeEndpointBuilder);
            }

            // The RoutePattern, RequestDelegate, Order and DisplayName can all be overridden by non-group-aware conventions.
            // Unlike with metadata, if a convention is applied to a group that changes any of these, I would expect these
            // to be overridden as there's no reasonable way to merge these properties.
            wrappedEndpoints[i] = (RouteEndpoint)routeEndpointBuilder.Build();
        }

        return wrappedEndpoints;
    }

    // We don't implement DebuggerDisplay directly on the EndpointDataSource base type because this could have side effects.
    internal static string GetDebuggerDisplayStringForEndpoints(IReadOnlyList<Endpoint>? endpoints)
    {