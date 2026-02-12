// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
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