using System.ComponentModel;

namespace Microsoft.EntityFrameworkCore;

/// <summary>
///     A <see cref="DbSet{TEntity}" /> can be used to query and save instances of <typeparamref name="TEntity" />.
///     LINQ queries against a <see cref="DbSet{TEntity}" /> will be translated into queries against the database.
/// </summary>
/// <remarks>
///     <para>
///         The results of a LINQ query against a <see cref="DbSet{TEntity}" /> will contain the results
///         returned from the database and may not reflect changes made in the context that have not
///         been persisted to the database. For example, the results will not contain newly added entities
///         and may still contain entities that are marked for deletion.
///     </para>
///     <para>
///         Depending on the database being used, some parts of a LINQ query against a <see cref="DbSet{TEntity}" />
///         may be evaluated in memory rather than being translated into a database query.
///     </para>
///     <para>
///         <see cref="DbSet{TEntity}" /> objects are usually obtained from a <see cref="DbSet{TEntity}" />
///         property on a derived <see cref="DbContext" /> or from the <see cref="DbContext.Set{TEntity}()" />
///         method.
///     </para>
///     <para>
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