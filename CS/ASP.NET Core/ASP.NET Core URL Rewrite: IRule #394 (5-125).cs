
/// <summary>
/// Represents a rule.
/// </summary>
public interface IRule
{
    /// <summary>
    /// Applies the rule.
    /// Implementations of ApplyRule should set the value for <see cref="RewriteContext.Result"/>
    /// (defaults to RuleResult.ContinueRules)
    /// </summary>
    /// <param name="context"></param>
    void ApplyRule(RewriteContext context);
}

