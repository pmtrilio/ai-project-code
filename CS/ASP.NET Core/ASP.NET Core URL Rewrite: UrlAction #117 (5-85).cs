
internal abstract class UrlAction
{
    protected Pattern? Url { get; set; }

    public abstract void ApplyAction(RewriteContext context, BackReferenceCollection? ruleBackReferences, BackReferenceCollection? conditionBackReferences);
}
