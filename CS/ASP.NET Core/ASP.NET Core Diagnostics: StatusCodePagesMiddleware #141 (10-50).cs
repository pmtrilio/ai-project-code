
/// <summary>
/// A middleware for generating the response body of error status codes with no body.
/// </summary>
public class StatusCodePagesMiddleware
{
    private readonly RequestDelegate _next;
    private readonly StatusCodePagesOptions _options;

    /// <summary>
    /// Creates a new <see cref="StatusCodePagesMiddleware"/>
    /// </summary>
    /// <param name="next">The <see cref="RequestDelegate"/> representing the next middleware in the pipeline.</param>
    /// <param name="options">The options for configuring the middleware.</param>
    public StatusCodePagesMiddleware(RequestDelegate next, IOptions<StatusCodePagesOptions> options)
    {
        _next = next;
        _options = options.Value;
        if (_options.HandleAsync == null)
        {
            throw new ArgumentException("Missing options.HandleAsync implementation.");
        }
    }

    /// <summary>
    /// Executes the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/> for the current request.</param>
    /// <returns>A task that represents the execution of this middleware.</returns>
    public async Task Invoke(HttpContext context)
    {
        var statusCodeFeature = new StatusCodePagesFeature();
        context.Features.Set<IStatusCodePagesFeature>(statusCodeFeature);
        var endpoint = context.GetEndpoint();
        var shouldCheckEndpointAgain = endpoint is null;

        if (HasSkipStatusCodePagesMetadata(endpoint))
        {
            statusCodeFeature.Enabled = false;
        }
