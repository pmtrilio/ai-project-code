// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace Serilog;

/// <summary>
/// An optional static entry point for logging that can be easily referenced
/// by different parts of an application. To configure the <see cref="Log"/>
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