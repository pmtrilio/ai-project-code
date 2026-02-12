// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

using System.Diagnostics;
// ReSharper disable IntroduceOptionalParameters.Global

namespace Serilog.Events;

/// <summary>
/// A log event.
/// </summary>
public class LogEvent
{
    readonly Dictionary<string, LogEventPropertyValue> _properties;
    ActivityTraceId _traceId;
    ActivitySpanId _spanId;

    LogEvent(
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
    /// <param name="spanId">The id of the span that was active when the event was created, if any.</param>
    /// <exception cref="ArgumentNullException">When <paramref name="messageTemplate"/> is <code>null</code></exception>
    /// <exception cref="ArgumentNullException">When <paramref name="properties"/> is <code>null</code></exception>
    [CLSCompliant(false)]
    public LogEvent(DateTimeOffset timestamp, LogEventLevel level, Exception? exception, MessageTemplate messageTemplate, IEnumerable<LogEventProperty> properties, ActivityTraceId traceId, ActivitySpanId spanId)
        : this(timestamp, level, exception, messageTemplate, new Dictionary<string, LogEventPropertyValue>(), traceId, spanId)
    {
        Guard.AgainstNull(properties);

        foreach (var property in properties)
            AddOrUpdateProperty(property);
    }

    internal LogEvent(DateTimeOffset timestamp, LogEventLevel level, Exception? exception, MessageTemplate messageTemplate, EventProperty[] properties, ActivityTraceId traceId, ActivitySpanId spanId)
        : this(timestamp, level, exception, messageTemplate, new Dictionary<string, LogEventPropertyValue>(Guard.AgainstNull(properties).Length), traceId, spanId)
    {
        for (var i = 0; i < properties.Length; ++i)
            _properties[properties[i].Name] = properties[i].Value;
    }

    /// <summary>
    /// The time at which the event occurred.
    /// </summary>
    public DateTimeOffset Timestamp { get; }

    /// <summary>
    /// The level of the event.
    /// </summary>
    public LogEventLevel Level { get; }

    /// <summary>
    /// The id of the trace that was active when the event was created, if any.
    /// </summary>
    [CLSCompliant(false)]
    public ActivityTraceId? TraceId => _traceId == default ? null : _traceId;

    /// <summary>
    /// The id of the span that was active when the event was created, if any.
    /// </summary>
    [CLSCompliant(false)]