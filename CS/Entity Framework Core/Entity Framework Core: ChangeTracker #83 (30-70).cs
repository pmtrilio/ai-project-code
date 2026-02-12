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