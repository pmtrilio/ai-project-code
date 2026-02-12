/// </remarks>
public abstract class QueryContext
{
    private IStateManager? _stateManager;

    /// <summary>
    ///     <para>
    ///         Creates a new <see cref="QueryContext" /> instance.
    ///     </para>
    ///     <para>
    ///         This type is typically used by database providers (and other extensions). It is generally
    ///         not used in application code.
    ///     </para>
    /// </summary>
    /// <param name="dependencies">The dependencies to use.</param>
    protected QueryContext(QueryContextDependencies dependencies)
    {
        Dependencies = dependencies;
        Context = dependencies.CurrentContext.Context;
    }

    /// <summary>
    ///     The current <see cref="DbContext" /> in using while executing the query.
    /// </summary>
    public virtual DbContext Context { get; }

    /// <summary>
    ///     The query parameter used in the query query.
    /// </summary>
    public virtual Dictionary<string, object?> Parameters { get; } = new();

    /// <summary>
    ///     Dependencies for this service.
    /// </summary>
    protected virtual QueryContextDependencies Dependencies { get; }

    /// <summary>
    ///     Sets the navigation for given entity as loaded.
    /// </summary>
    /// <param name="entity">The entity instance.</param>
    /// <param name="navigation">The navigation property.</param>
    public virtual void SetNavigationIsLoaded(object entity, INavigationBase navigation)
        // InitializeStateManager will populate the field before calling here
        => _stateManager!.TryGetEntry(entity)!.SetIsLoaded(navigation);

    /// <summary>
    ///     The execution strategy to use while executing the query.
    /// </summary>
    public virtual IExecutionStrategy ExecutionStrategy
        => Dependencies.ExecutionStrategy;

    /// <summary>
    ///     The concurrency detector to use while executing the query.
    /// </summary>
    public virtual IConcurrencyDetector ConcurrencyDetector
        => Dependencies.ConcurrencyDetector;

    /// <summary>
    ///     The exception detector to use while executing the query.
    /// </summary>
    public virtual IExceptionDetector ExceptionDetector
        => Dependencies.ExceptionDetector;

    /// <summary>
    ///     The cancellation token to use while executing the query.
    /// </summary>
    public virtual CancellationToken CancellationToken { get; set; }

    /// <summary>
    ///     The command logger to use while executing the query.
    /// </summary>
    public virtual IDiagnosticsLogger<DbLoggerCategory.Database.Command> CommandLogger
        => Dependencies.CommandLogger;

    /// <summary>
    ///     The query logger to use while executing the query.
    /// </summary>
    public virtual IDiagnosticsLogger<DbLoggerCategory.Query> QueryLogger
        => Dependencies.QueryLogger;

    /// <summary>