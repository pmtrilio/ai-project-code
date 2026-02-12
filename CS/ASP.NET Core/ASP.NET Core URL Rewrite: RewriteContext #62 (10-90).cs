namespace Microsoft.AspNetCore.Rewrite;

/// <summary>
/// A context object for <see cref="RewriteMiddleware"/>
/// </summary>
public class RewriteContext
{
    /// <summary>
    /// Gets and sets the <see cref="HttpContext"/>
    /// </summary>
    public HttpContext HttpContext { get; set; } = default!;

    /// <summary>
    /// Gets and sets the File Provider for file and directory checks.
    /// </summary>
    public IFileProvider StaticFileProvider { get; set; } = default!;

    /// <summary>
    /// Gets and sets the logger
    /// </summary>
    public ILogger Logger { get; set; } = NullLogger.Instance;

    /// <summary>
    /// A shared result that is set appropriately by each rule for the next action that
    /// should be taken. See <see cref="RuleResult"/>
    /// </summary>
    public RuleResult Result { get; set; }

    internal StringBuilder Builder { get; set; } = new StringBuilder(64);
}
