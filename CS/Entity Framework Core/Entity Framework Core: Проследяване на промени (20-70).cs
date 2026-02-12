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

        StateManager = stateManager;
        ChangeDetector = changeDetector;
        _model = (IRuntimeModel)model;
        GraphIterator = graphIterator;
    }

    /// <summary>
    ///     Gets or sets a value indicating whether the <see cref="DetectChanges()" /> method is called
    ///     automatically by methods of <see cref="DbContext" /> and related classes.
    /// </summary>
    /// <remarks>
    ///     <para>
    ///         The default value is true. This ensures the context is aware of any changes to tracked entity instances
    ///         before performing operations such as <see cref="DbContext.SaveChanges()" /> or returning change tracking
    ///         information. If you disable automatic detect changes then you must ensure that
    ///         <see cref="DetectChanges()" /> is called when entity instances have been modified.
    ///         Failure to do so may result in some changes not being persisted during
    ///         <see cref="DbContext.SaveChanges()" /> or out-of-date change tracking information being returned.
    ///     </para>
    ///     <para>