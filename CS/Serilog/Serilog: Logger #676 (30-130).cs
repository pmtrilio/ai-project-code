{
    static readonly object[] NoPropertyValues = Array.Empty<object>();
    static readonly LogEventProperty[] NoProperties = Array.Empty<LogEventProperty>();

    readonly MessageTemplateProcessor _messageTemplateProcessor;
    readonly ILogEventSink _sink;
    readonly Action? _dispose;
#if FEATURE_ASYNCDISPOSABLE
    readonly Func<ValueTask>? _disposeAsync;
#endif
    readonly ILogEventEnricher _enricher;

    // It's important that checking minimum level is a very
    // quick (CPU-cacheable) read in the simple case, hence
    // we keep a separate field from the switch, which may
    // not be specified. If it is, we'll set _minimumLevel
    // to its lower limit and fall through to the secondary check.
    readonly LogEventLevel _minimumLevel;
    readonly LoggingLevelSwitch? _levelSwitch;
    readonly LevelOverrideMap? _overrideMap;

    internal Logger(
        MessageTemplateProcessor messageTemplateProcessor,
        LogEventLevel minimumLevel,
        LoggingLevelSwitch? levelSwitch,
        ILogEventSink sink,
        ILogEventEnricher enricher,
        Action? dispose,
#if FEATURE_ASYNCDISPOSABLE
        Func<ValueTask>? disposeAsync,
#endif
        LevelOverrideMap? overrideMap)
    {
        _messageTemplateProcessor = messageTemplateProcessor;
        _minimumLevel = minimumLevel;
        _sink = sink;
        _dispose = dispose;
#if FEATURE_ASYNCDISPOSABLE
        _disposeAsync = disposeAsync;
#endif
        _levelSwitch = levelSwitch;
        _overrideMap = overrideMap;
        _enricher = enricher;
    }

    internal bool HasOverrideMap => _overrideMap != null;

    /// <summary>
    /// Create a logger that enriches log events via the provided enrichers.
    /// </summary>
    /// <param name="enricher">Enricher that applies in the context.</param>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public ILogger ForContext(ILogEventEnricher enricher)
    {
        if (enricher == null!)
            return this; // No context here, so little point writing to SelfLog.

        return new Logger(
            _messageTemplateProcessor,
            _minimumLevel,
            _levelSwitch,
            this,
            enricher,
            null,
#if FEATURE_ASYNCDISPOSABLE
            null,
#endif
            _overrideMap);
    }

    /// <summary>
    /// Create a logger that enriches log events via the provided enrichers.
    /// </summary>
    /// <param name="enrichers">Enrichers that apply in the context.</param>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public ILogger ForContext(IEnumerable<ILogEventEnricher> enrichers)
    {
        if (enrichers == null!)
            return this; // No context here, so little point writing to SelfLog.

        return ForContext(new SafeAggregateEnricher(enrichers));
    }

    /// <summary>
    /// Create a logger that enriches log events with the specified property.
    /// </summary>
    /// <param name="propertyName">The name of the property. Must be non-empty.</param>
    /// <param name="value">The property value.</param>
    /// <param name="destructureObjects">If <see langword="true"/>, the value will be serialized as a structured
    /// object if possible; if <see langword="false"/>, the object will be recorded as a scalar or simple array.</param>
    /// <returns>A logger that will enrich log events as specified.</returns>
    public ILogger ForContext(string propertyName, object? value, bool destructureObjects = false)
    {
        if (!LogEventProperty.IsValidName(propertyName))
        {
            SelfLog.WriteLine("Attempt to call ForContext() with invalid property name `{0}` (value: `{1}`)", propertyName, value);
            return this;
        }

        // It'd be nice to do the destructuring lazily, but unfortunately `value` may be mutated between
        // now and the first log event written.