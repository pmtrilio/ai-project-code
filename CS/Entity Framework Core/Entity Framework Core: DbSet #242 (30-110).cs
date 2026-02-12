///         Entity Framework Core does not support multiple parallel operations being run on the same <see cref="DbContext" />
///         instance. This includes both parallel execution of async queries and any explicit concurrent use from multiple threads.
///         Therefore, always await async calls immediately, or use separate DbContext instances for operations that execute
///         in parallel. See <see href="https://aka.ms/efcore-docs-threading">Avoiding DbContext threading issues</see> for more information
///         and examples.
///     </para>
///     <para>
///         See <see href="https://aka.ms/efcore-docs-dbcontext">DbContext lifetime, configuration, and initialization</see>,
///         <see href="https://aka.ms/efcore-docs-query">Querying data with EF Core</see>, and
///         <see href="https://aka.ms/efcore-docs-change-tracking">Changing tracking</see> for more information and examples.
///     </para>
/// </remarks>
/// <typeparam name="TEntity">The type of entity being operated on by this set.</typeparam>
public abstract class DbSet<[DynamicallyAccessedMembers(IEntityType.DynamicallyAccessedMemberTypes)] TEntity>
    : IQueryable<TEntity>, IInfrastructure<IServiceProvider>, IListSource
    where TEntity : class
{
    /// <summary>
    ///     The <see cref="IEntityType" /> metadata associated with this set.
    /// </summary>
    public abstract IEntityType EntityType { get; }

    /// <summary>
    ///     Returns this object typed as <see cref="IAsyncEnumerable{T}" />.
    /// </summary>
    /// <remarks>
    ///     See <see href="https://aka.ms/efcore-docs-query">Querying data with EF Core</see> for more information and examples.
    /// </remarks>
    /// <returns>This object.</returns>
    public virtual IAsyncEnumerable<TEntity> AsAsyncEnumerable()
        => (IAsyncEnumerable<TEntity>)this;

    /// <summary>
    ///     Returns this object typed as <see cref="IQueryable{T}" />.
    /// </summary>
    /// <remarks>
    ///     <para>
    ///         This is a convenience method to help with disambiguation of extension methods in the same
    ///         namespace that extend both interfaces.
    ///     </para>
    ///     <para>
    ///         See <see href="https://aka.ms/efcore-docs-query">Querying data with EF Core</see> for more information and examples.
    ///     </para>
    /// </remarks>
    /// <returns>This object.</returns>
    public virtual IQueryable<TEntity> AsQueryable()
        => this;

    /// <summary>
    ///     Gets a <see cref="LocalView{TEntity}" /> that represents a local view of all Added, Unchanged,
    ///     and Modified entities in this set.
    /// </summary>
    /// <remarks>
    ///     <para>
    ///         This local view will stay in sync as entities are added or removed from the context. Likewise, entities
    ///         added to or removed from the local view will automatically be added to or removed
    ///         from the context.
    ///     </para>
    ///     <para>
    ///         This property can be used for data binding by populating the set with data, for example by using the
    ///         <see cref="EntityFrameworkQueryableExtensions.Load{TSource}" /> extension method,
    ///         and then binding to the local data through this property by calling
    ///         <see cref="LocalView{TEntity}.ToObservableCollection" /> for WPF binding, or
    ///         <see cref="LocalView{TEntity}.ToBindingList" /> for WinForms.
    ///     </para>
    ///     <para>
    ///         Note that this method calls <see cref="ChangeTracker.DetectChanges" /> unless
    ///         <see cref="ChangeTracker.AutoDetectChangesEnabled" /> has been set to <see langword="false" />.
    ///     </para>
    ///     <para>
    ///         See <see href="https://aka.ms/efcore-docs-local-views">Local views of tracked entities in EF Core</see> for more information and
    ///         examples.
    ///     </para>
    /// </remarks>
    public virtual LocalView<TEntity> Local
        => throw new NotSupportedException();

    /// <summary>
    ///     Finds an entity with the given primary key values. If an entity with the given primary key values
    ///     is being tracked by the context, then it is returned immediately without making a request to the
    ///     database. Otherwise, a query is made to the database for an entity with the given primary key values