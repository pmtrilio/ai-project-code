// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace Serilog.Parsing;

/// <summary>
/// Parses message template strings into sequences of text or property
/// tokens.
/// </summary>
public class MessageTemplateParser : IMessageTemplateParser
{
    static readonly TextToken EmptyTextToken = new("");

    /// <summary>
    /// Construct a <see cref="MessageTemplateParser"/>.
    /// </summary>
    public MessageTemplateParser()
    {
    }

    /// <summary>
    /// Parse the supplied message template.
    /// </summary>
    /// <param name="messageTemplate">The message template to parse.</param>
    /// <returns>A sequence of text or property tokens. Where the template
    /// is not syntactically valid, text tokens will be returned. The parser
    /// will make a best effort to extract valid property tokens even in the
    /// presence of parsing issues.</returns>
    /// <exception cref="ArgumentNullException">When <paramref name="messageTemplate"/> is <code>null</code></exception>
    public MessageTemplate Parse(string messageTemplate)
    {
        Guard.AgainstNull(messageTemplate);

        return new(messageTemplate, Tokenize(messageTemplate));
    }

    IEnumerable<MessageTemplateToken> Tokenize(string messageTemplate)
    {
        if (messageTemplate.Length == 0)