
namespace Microsoft.AspNetCore.StaticFiles;

/// <summary>
/// Enables directory browsing
/// </summary>
public class DirectoryBrowserMiddleware
{
    private readonly DirectoryBrowserOptions _options;
    private readonly PathString _matchUrl;
    private readonly RequestDelegate _next;
    private readonly IDirectoryFormatter _formatter;
    private readonly IFileProvider _fileProvider;

    /// <summary>
    /// Creates a new instance of the SendFileMiddleware. Using <see cref="HtmlEncoder.Default"/> instance.
    /// </summary>
    /// <param name="next">The next middleware in the pipeline.</param>
    /// <param name="hostingEnv">The <see cref="IWebHostEnvironment"/> used by this middleware.</param>
    /// <param name="options">The configuration for this middleware.</param>
    public DirectoryBrowserMiddleware(RequestDelegate next, IWebHostEnvironment hostingEnv, IOptions<DirectoryBrowserOptions> options)
        : this(next, hostingEnv, HtmlEncoder.Default, options)
    {
    }

    /// <summary>
    /// Creates a new instance of the SendFileMiddleware.
    /// </summary>
    /// <param name="next">The next middleware in the pipeline.</param>
    /// <param name="hostingEnv">The <see cref="IWebHostEnvironment"/> used by this middleware.</param>
    /// <param name="encoder">The <see cref="HtmlEncoder"/> used by the default <see cref="HtmlDirectoryFormatter"/>.</param>
    /// <param name="options">The configuration for this middleware.</param>
    public DirectoryBrowserMiddleware(RequestDelegate next, IWebHostEnvironment hostingEnv, HtmlEncoder encoder, IOptions<DirectoryBrowserOptions> options)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(hostingEnv);
        ArgumentNullException.ThrowIfNull(encoder);
        ArgumentNullException.ThrowIfNull(options);

        _next = next;
        _options = options.Value;