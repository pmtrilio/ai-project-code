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
        {
            yield return EmptyTextToken;
            yield break;
        }

        var nextIndex = 0;
        while (true)
        {
            var beforeText = nextIndex;
            var tt = ParseTextToken(nextIndex, messageTemplate, out nextIndex);
            if (nextIndex > beforeText)
                yield return tt;

            if (nextIndex == messageTemplate.Length)
                yield break;

            var beforeProp = nextIndex;
            var pt = ParsePropertyToken(nextIndex, messageTemplate, out nextIndex);
            if (beforeProp < nextIndex)
                yield return pt;

            if (nextIndex == messageTemplate.Length)
                yield break;
        }
    }

    MessageTemplateToken ParsePropertyToken(int startAt, string messageTemplate, out int next)
    {
        var first = startAt;
        startAt++;

        startAt = messageTemplate.IndexOf('}', startAt);
        if (startAt == -1)
        {
            next = messageTemplate.Length;
            return new TextToken(messageTemplate[first..]);
        }

        next = startAt + 1;

        var rawText = messageTemplate.Substring(first, next - first);
        var tagContent = rawText.Substring(1, next - (first + 2));
        if (tagContent.Length == 0)
            return new TextToken(rawText);

        if (!TrySplitTagContent(tagContent, out var propertyNameAndDestructuring, out var format, out var alignment))
            return new TextToken(rawText);

        var propertyName = propertyNameAndDestructuring;
        var destructuring = Destructuring.Default;
        if (propertyName.Length != 0 && TryGetDestructuringHint(propertyName[0], out destructuring))
            propertyName = propertyName[1..];

        if (propertyName.Length == 0)
        {
            return new TextToken(rawText);
        }

        if (char.IsDigit(propertyName[0]))
        {
            for (var i = 0; i < propertyName.Length; ++i)
            {
                var c = propertyName[i];
                if (!char.IsDigit(c))
                    return new TextToken(rawText);
            }
        }
        else
        {
            var beginIdent = true;
            for (var i = 0; i < propertyName.Length; ++i)
            {
                var c = propertyName[i];
                if (!TryContinuePropertyName(c, ref beginIdent))
                    return new TextToken(rawText);
            }

            if (beginIdent)
            {
                return new TextToken(rawText);
            }
        }

        if (format != null)
        {
            for (var i = 0; i < format.Length; ++i)
            {
                var c = format[i];
                if (!IsValidInFormat(c))
                    return new TextToken(rawText);