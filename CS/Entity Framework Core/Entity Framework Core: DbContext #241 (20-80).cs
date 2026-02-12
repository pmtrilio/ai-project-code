///         Therefore, always await async calls immediately, or use separate DbContext instances for operations that execute
///         in parallel. See <see href="https://aka.ms/efcore-docs-threading">Avoiding DbContext threading issues</see> for more information
///         and examples.
///     </para>
///     <para>
///         Typically you create a class that derives from DbContext and contains <see cref="DbSet{TEntity}" />
///         properties for each entity in the model. If the <see cref="DbSet{TEntity}" /> properties have a public setter,
///         they are automatically initialized when the instance of the derived context is created.
///     </para>
///     <para>
///         Override the <see cref="OnConfiguring(DbContextOptionsBuilder)" /> method to configure the database (and
///         other options) to be used for the context. Alternatively, if you would rather perform configuration externally
///         instead of inline in your context, you can use <see cref="DbContextOptionsBuilder{TContext}" />
///         (or <see cref="DbContextOptionsBuilder" />) to externally create an instance of <see cref="DbContextOptions{TContext}" />
///         (or <see cref="DbContextOptions" />) and pass it to a base constructor of <see cref="DbContext" />.
///     </para>
///     <para>
///         The model is discovered by running a set of conventions over the entity classes found in the
///         <see cref="DbSet{TEntity}" /> properties on the derived context. To further configure the model that
///         is discovered by convention, you can override the <see cref="OnModelCreating(ModelBuilder)" /> method.
///     </para>
///     <para>
///         See <see href="https://aka.ms/efcore-docs-dbcontext">DbContext lifetime, configuration, and initialization</see>,
///         <see href="https://aka.ms/efcore-docs-query">Querying data with EF Core</see>,
///         <see href="https://aka.ms/efcore-docs-change-tracking">Changing tracking</see>, and
///         <see href="https://aka.ms/efcore-docs-saving-data">Saving data with EF Core</see> for more information and examples.
///     </para>
/// </remarks>
public class DbContext :
    IInfrastructure<IServiceProvider>,
    IDbContextDependencies,
    IDbSetCache,
    IDbContextPoolable
{
    private readonly DbContextOptions _options;

    private Dictionary<(Type Type, string? Name), object>? _sets;
    private IDbContextServices? _contextServices;
    private IDbContextDependencies? _dbContextDependencies;
    private DatabaseFacade? _database;
    private ChangeTracker? _changeTracker;

    private IServiceScope? _serviceScope;
    private DbContextLease _lease = DbContextLease.InactiveLease;
    private DbContextPoolConfigurationSnapshot? _configurationSnapshot;
    private List<IResettableService>? _cachedResettableServices;
    private bool _initializing;
    private bool _disposed;

    private readonly Guid _contextId = Guid.NewGuid();
    private int _leaseCount;

    /// <summary>
    ///     Initializes a new instance of the <see cref="DbContext" /> class. The
    ///     <see cref="OnConfiguring(DbContextOptionsBuilder)" />
    ///     method will be called to configure the database (and other options) to be used for this context.
    /// </summary>
    /// <remarks>
    ///     See <see href="https://aka.ms/efcore-docs-dbcontext">DbContext lifetime, configuration, and initialization</see>
    ///     for more information and examples.
    /// </remarks>