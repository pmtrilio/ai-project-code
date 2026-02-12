using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.StaticFiles;

/// <summary>
/// This examines a directory path and determines if there is a default file present.
/// If so the file name is appended to the path and execution continues.
/// Note we don't just serve the file because it may require interpretation.
/// </summary>
public class DefaultFilesMiddleware
{
    private readonly DefaultFilesOptions _options;
    private readonly PathString _matchUrl;
    private readonly RequestDelegate _next;
    private readonly IFileProvider _fileProvider;

    /// <summary>
    /// Creates a new instance of the DefaultFilesMiddleware.
    /// </summary>
    /// <param name="next">The next middleware in the pipeline.</param>
    /// <param name="hostingEnv">The <see cref="IWebHostEnvironment"/> used by this middleware.</param>
    /// <param name="options">The configuration options for this middleware.</param>
    public DefaultFilesMiddleware(RequestDelegate next, IWebHostEnvironment hostingEnv, IOptions<DefaultFilesOptions> options)
    {
        ArgumentNullException.ThrowIfNull(next);
        ArgumentNullException.ThrowIfNull(hostingEnv);
        ArgumentNullException.ThrowIfNull(options);

        _next = next;
        _options = options.Value;
        _fileProvider = _options.FileProvider ?? Helpers.ResolveFileProvider(hostingEnv);
        _matchUrl = _options.RequestPath;
    }

    /// <summary>
    /// This examines the request to see if it matches a configured directory, and if there are any files with the
    /// configured default names in that directory.  If so this will append the corresponding file name to the request
    /// path for a later middleware to handle.