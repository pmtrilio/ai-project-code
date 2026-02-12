using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.Builder;

/// <summary>
/// Extension methods for adding the <see cref="RequestLocalizationMiddleware"/> to an application.
/// </summary>
public static class ApplicationBuilderExtensions
{
    /// <summary>
    /// Adds the <see cref="RequestLocalizationMiddleware"/> to automatically set culture information for
    /// requests based on information provided by the client.
    /// </summary>
    /// <param name="app">The <see cref="IApplicationBuilder"/>.</param>
    /// <returns>The <see cref="IApplicationBuilder"/>.</returns>
    public static IApplicationBuilder UseRequestLocalization(this IApplicationBuilder app)
    {
        ArgumentNullException.ThrowIfNull(app);

        return app.UseMiddleware<RequestLocalizationMiddleware>();
    }

    /// <summary>
    /// Adds the <see cref="RequestLocalizationMiddleware"/> to automatically set culture information for
    /// requests based on information provided by the client.
    /// </summary>
    /// <param name="app">The <see cref="IApplicationBuilder"/>.</param>
    /// <param name="options">The <see cref="RequestLocalizationOptions"/> to configure the middleware with.</param>
    /// <returns>The <see cref="IApplicationBuilder"/>.</returns>
    public static IApplicationBuilder UseRequestLocalization(
        this IApplicationBuilder app,
        RequestLocalizationOptions options)
    {
        ArgumentNullException.ThrowIfNull(app);
        ArgumentNullException.ThrowIfNull(options);

        return app.UseMiddleware<RequestLocalizationMiddleware>(Options.Create(options));
    }

    /// <summary>
    /// Adds the <see cref="RequestLocalizationMiddleware"/> to automatically set culture information for
    /// requests based on information provided by the client.
    /// </summary>
    /// <param name="app">The <see cref="IApplicationBuilder"/>.</param>
    /// <param name="optionsAction">A callback that configures the <see cref="RequestLocalizationOptions"/>.</param>
    /// <remarks>
    /// This will going to instantiate a new <see cref="RequestLocalizationOptions"/> that doesn't come from the services.
    /// </remarks>
    /// <returns>The <see cref="IApplicationBuilder"/>.</returns>
    public static IApplicationBuilder UseRequestLocalization(
        this IApplicationBuilder app,
        Action<RequestLocalizationOptions> optionsAction)
    {
        ArgumentNullException.ThrowIfNull(app);
        ArgumentNullException.ThrowIfNull(optionsAction);

        var options = new RequestLocalizationOptions();
        optionsAction.Invoke(options);

        return app.UseMiddleware<RequestLocalizationMiddleware>(Options.Create(options));
    }