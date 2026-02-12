        DateTimeOffset timestamp,
        LogEventLevel level,
        Exception? exception,
        MessageTemplate messageTemplate,
        Dictionary<string, LogEventPropertyValue> properties,
        ActivityTraceId traceId,
        ActivitySpanId spanId)
    {
        Timestamp = timestamp;
        Level = level;
        Exception = exception;
        _traceId = traceId;
        _spanId = spanId;
        MessageTemplate = Guard.AgainstNull(messageTemplate);
        _properties = Guard.AgainstNull(properties);
    }

    /// <summary>
    /// Construct a new <seealso cref="LogEvent"/>.
    /// </summary>
    /// <param name="timestamp">The time at which the event occurred.</param>
    /// <param name="level">The level of the event.</param>
    /// <param name="exception">An exception associated with the event, or null.</param>
    /// <param name="messageTemplate">The message template describing the event.</param>
    /// <param name="properties">Properties associated with the event, including those presented in <paramref name="messageTemplate"/>.</param>
    /// <exception cref="ArgumentNullException">When <paramref name="messageTemplate"/> is <code>null</code></exception>
    /// <exception cref="ArgumentNullException">When <paramref name="properties"/> is <code>null</code></exception>
    public LogEvent(DateTimeOffset timestamp, LogEventLevel level, Exception? exception, MessageTemplate messageTemplate, IEnumerable<LogEventProperty> properties)
        : this(timestamp, level, exception, messageTemplate, properties, default, default)
    {
    }

    /// <summary>
    /// Construct a new <seealso cref="LogEvent"/>.
    /// </summary>
    /// <param name="timestamp">The time at which the event occurred.</param>
    /// <param name="level">The level of the event.</param>
    /// <param name="exception">An exception associated with the event, or null.</param>
    /// <param name="messageTemplate">The message template describing the event.</param>
    /// <param name="properties">Properties associated with the event, including those presented in <paramref name="messageTemplate"/>.</param>
    /// <param name="traceId">The id of the trace that was active when the event was created, if any.</param>