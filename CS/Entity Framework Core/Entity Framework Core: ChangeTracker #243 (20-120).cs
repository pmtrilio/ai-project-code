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
    ///         See <see href="https://aka.ms/efcore-docs-change-tracking">EF Core change tracking</see> for more information and examples.
    ///     </para>
    /// </remarks>
    public virtual bool AutoDetectChangesEnabled { get; set; } = true;

    /// <summary>
    ///     Gets or sets a value indicating whether navigation properties for tracked entities
    ///     will be loaded on first access.
    /// </summary>
    /// <remarks>
    ///     The default value is true. However, lazy loading will only occur for navigation properties
    ///     of entities that have also been configured in the model for lazy loading.
    /// </remarks>
    public virtual bool LazyLoadingEnabled { get; set; } = true;

    /// <summary>
    ///     Gets or sets the tracking behavior for LINQ queries run against the context. Disabling change tracking
    ///     is useful for read-only scenarios because it avoids the overhead of setting up change tracking for each
    ///     entity instance. You should not disable change tracking if you want to manipulate entity instances and
    ///     persist those changes to the database using <see cref="DbContext.SaveChanges()" />.
    /// </summary>
    /// <remarks>
    ///     <para>
    ///         This method sets the default behavior for the context, but you can override this behavior for individual
    ///         queries using the <see cref="EntityFrameworkQueryableExtensions.AsNoTracking{TEntity}(IQueryable{TEntity})" />
    ///         and <see cref="EntityFrameworkQueryableExtensions.AsTracking{TEntity}(IQueryable{TEntity})" /> methods.
    ///     </para>
    ///     <para>
    ///         The default value is <see cref="Microsoft.EntityFrameworkCore.QueryTrackingBehavior.TrackAll" />. This means
    ///         the change tracker will keep track of changes for all entities that are returned from a LINQ query.
    ///     </para>
    /// </remarks>
    public virtual QueryTrackingBehavior QueryTrackingBehavior
    {
        get => _queryTrackingBehavior;
        set => _queryTrackingBehavior = value;
    }

    /// <summary>
    ///     Gets or sets a value indicating when a dependent/child entity will have its state
    ///     set to <see cref="EntityState.Deleted" /> once severed from a parent/principal entity
    ///     through either a navigation or foreign key property being set to null. The default
    ///     value is <see cref="CascadeTiming.Immediate" />.
    /// </summary>
    /// <remarks>
    ///     <para>
    ///         Dependent/child entities are only deleted automatically when the relationship
    ///         is configured with <see cref="DeleteBehavior.Cascade" />. This is set by default
    ///         for required relationships.
    ///     </para>