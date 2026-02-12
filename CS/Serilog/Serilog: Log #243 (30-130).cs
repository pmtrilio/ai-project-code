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
    /// </summary>
    public static async ValueTask CloseAndFlushAsync()
    {
        var logger = Interlocked.Exchange(ref _logger, Serilog.Core.Logger.None);

        if (logger is IAsyncDisposable asyncDisposable)
        {
            await asyncDisposable.DisposeAsync();
        }
        else
        {
            (logger as IDisposable)?.Dispose();
        }
    }
#endif

    /// <summary>
    /// Create a logger that enriches log events via the provided enrichers.
    /// </summary>
    /// <param name="enricher">Enricher that applies in the context.</param>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public static ILogger ForContext(ILogEventEnricher enricher)
    {
        return Logger.ForContext(enricher);
    }

    /// <summary>
    /// Create a logger that enriches log events via the provided enrichers.
    /// </summary>
    /// <param name="enrichers">Enrichers that apply in the context.</param>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public static ILogger ForContext(ILogEventEnricher[] enrichers)
    {
        return Logger.ForContext(enrichers);
    }

    /// <summary>
    /// Create a logger that enriches log events with the specified property.
    /// </summary>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public static ILogger ForContext(string propertyName, object? value, bool destructureObjects = false)
    {
        return Logger.ForContext(propertyName, value, destructureObjects);
    }

    /// <summary>
    /// Create a logger that marks log events as being from the specified
    /// source type.
    /// </summary>
    /// <typeparam name="TSource">Type generating log messages in the context.</typeparam>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public static ILogger ForContext<TSource>() => Logger.ForContext<TSource>();

    /// <summary>
    /// Create a logger that marks log events as being from the specified
    /// source type.
    /// </summary>
    /// <param name="source">Type generating log messages in the context.</param>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public static ILogger ForContext(Type source) => Logger.ForContext(source);

    /// <summary>
    /// Write an event to the log.
    /// </summary>
    /// <param name="logEvent">The event to write.</param>
    public static void Write(LogEvent logEvent)
    {
        Logger.Write(logEvent);
    }
