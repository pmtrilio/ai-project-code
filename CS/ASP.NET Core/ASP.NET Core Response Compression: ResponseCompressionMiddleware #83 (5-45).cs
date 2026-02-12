using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Http.Features;

namespace Microsoft.AspNetCore.ResponseCompression;

/// <summary>
/// Enable HTTP response compression.
/// </summary>
public class ResponseCompressionMiddleware
{
    private readonly RequestDelegate _next;
    private readonly IResponseCompressionProvider _provider;

    /// <summary>
    /// Initialize the Response Compression middleware.
    /// </summary>
    /// <param name="next">The delegate representing the remaining middleware in the request pipeline.</param>
    /// <param name="provider">The <see cref="IResponseCompressionProvider"/>.</param>
    public ResponseCompressionMiddleware(RequestDelegate next, IResponseCompressionProvider provider)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(provider);

        _next = next;
        _provider = provider;
    }

    /// <summary>
    /// Invoke the middleware.
    /// </summary>
    /// <param name="context">The <see cref="HttpContext"/>.</param>
    /// <returns>A task that represents the execution of this middleware.</returns>
    public Task Invoke(HttpContext context)
    {
        if (!_provider.CheckRequestAcceptsCompression(context))
        {
            return _next(context);
        }
        return InvokeCore(context);
    }
