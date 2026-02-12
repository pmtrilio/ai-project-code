
internal sealed class RewriteRule : IRule
{
    private readonly TimeSpan _regexTimeout = TimeSpan.FromSeconds(1);
    public Regex InitialMatch { get; }
    public string Replacement { get; }
    public bool StopProcessing { get; }
    public RewriteRule(string regex, string replacement, bool stopProcessing)
    {
        ArgumentException.ThrowIfNullOrEmpty(regex);
        ArgumentException.ThrowIfNullOrEmpty(replacement);

        InitialMatch = new Regex(regex, RegexOptions.Compiled | RegexOptions.CultureInvariant, _regexTimeout);
        Replacement = replacement;
        StopProcessing = stopProcessing;
    }

    public void ApplyRule(RewriteContext context)
    {
        var path = context.HttpContext.Request.Path;
        Match initMatchResults;
        if (path == PathString.Empty)
        {
            initMatchResults = InitialMatch.Match(path.ToString());
        }
        else
        {
            initMatchResults = InitialMatch.Match(path.ToString().Substring(1));
        }

        if (initMatchResults.Success)
        {
            var result = initMatchResults.Result(Replacement);
            var request = context.HttpContext.Request;

            if (StopProcessing)
            {
                context.Result = RuleResult.SkipRemainingRules;
            }

            if (string.IsNullOrEmpty(result))