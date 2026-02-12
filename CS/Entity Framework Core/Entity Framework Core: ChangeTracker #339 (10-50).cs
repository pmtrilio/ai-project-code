/// <summary>
///     Provides access to change tracking information and operations for entity instances the context is tracking.
///     Instances of this class are typically obtained from <see cref="DbContext.ChangeTracker" /> and it is not designed
///     to be directly constructed in your application code.
/// </summary>
/// <remarks>
///     See <see href="https://aka.ms/efcore-docs-change-tracking">EF Core change tracking</see> for more information and examples.
/// </remarks>
public class ChangeTracker : IResettableService
{
    private readonly IRuntimeModel _model;
    private QueryTrackingBehavior _queryTrackingBehavior;
    private readonly QueryTrackingBehavior _defaultQueryTrackingBehavior;

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    [EntityFrameworkInternal]
    public ChangeTracker(
        DbContext context,
        IStateManager stateManager,
        IChangeDetector changeDetector,
        IModel model,
        IEntityEntryGraphIterator graphIterator)
    {
        Context = context;

        _defaultQueryTrackingBehavior
            = context
                .GetService<IDbContextOptions>()
                .Extensions
                .OfType<CoreOptionsExtension>()
                .FirstOrDefault()
                ?.QueryTrackingBehavior
            ?? QueryTrackingBehavior.TrackAll;

        _queryTrackingBehavior = _defaultQueryTrackingBehavior;
