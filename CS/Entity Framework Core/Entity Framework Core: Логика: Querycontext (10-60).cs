///         The principal data structure used by a compiled query during execution.
///     </para>
///     <para>
///         This type is typically used by database providers (and other extensions). It is generally
///         not used in application code.
///     </para>
/// </summary>
/// <remarks>
///     See <see href="https://aka.ms/efcore-docs-providers">Implementation of database providers and extensions</see>
///     and <see href="https://aka.ms/efcore-docs-how-query-works">How EF Core queries work</see> for more information and examples.
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