/// set the Logger static property to a logger instance.
/// </summary>
/// <example>
/// Log.Logger = new LoggerConfiguration()
///     .WithConsoleSink()
///     .CreateLogger();
///
/// var thing = "World";
/// Log.Logger.Information("Hello, {Thing}!", thing);
/// </example>
/// <remarks>
/// The methods on <see cref="Log"/> (and its dynamic sibling <see cref="ILogger"/>) are guaranteed
/// never to throw exceptions. Methods on all other types may.
/// </remarks>
public static class Log
{
    static ILogger _logger = Serilog.Core.Logger.None;

    /// <summary>
    /// The globally-shared logger.
    /// </summary>
    /// <exception cref="ArgumentNullException">When <paramref name="value"/> is <code>null</code></exception>
    public static ILogger Logger
    {
        get => _logger;
        set => _logger = Guard.AgainstNull(value);
    }

    /// <summary>
    /// Resets <see cref="Logger"/> to the default and disposes the original if possible
    /// </summary>
    public static void CloseAndFlush()
    {
        var logger = Interlocked.Exchange(ref _logger, Serilog.Core.Logger.None);

        (logger as IDisposable)?.Dispose();
    }

#if FEATURE_ASYNCDISPOSABLE
    /// <summary>
    /// Resets <see cref="Logger"/> to the default and disposes the original if possible