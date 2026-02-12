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
    [RequiresUnreferencedCode(
         "EF Core isn't fully compatible with trimming, and running the application may generate unexpected runtime failures. "
         + "Some specific coding pattern are usually required to make trimming work properly, see https://aka.ms/efcore-docs-trimming for "
         + "more details."), RequiresDynamicCode(
         "EF Core isn't fully compatible with NativeAOT, and running the application may generate unexpected runtime failures.")]
    protected DbContext()
        : this(new DbContextOptions<DbContext>())
    {
    }

    /// <summary>
    ///     Initializes a new instance of the <see cref="DbContext" /> class using the specified options.
    ///     The <see cref="OnConfiguring(DbContextOptionsBuilder)" /> method will still be called to allow further
    ///     configuration of the options.
    /// </summary>
    /// <remarks>
    ///     See <see href="https://aka.ms/efcore-docs-dbcontext">DbContext lifetime, configuration, and initialization</see> and
    ///     <see href="https://aka.ms/efcore-docs-dbcontext-options">Using DbContextOptions</see> for more information and examples.
    /// </remarks>
    /// <param name="options">The options for this context.</param>
    [RequiresUnreferencedCode(
         "EF Core isn't fully compatible with trimming, and running the application may generate unexpected runtime failures. "
         + "Some specific coding pattern are usually required to make trimming work properly, see https://aka.ms/efcore-docs-trimming for "
         + "more details."), RequiresDynamicCode(
         "EF Core isn't fully compatible with NativeAOT, and running the application may generate unexpected runtime failures.")]
    public DbContext(DbContextOptions options)
    {
        Check.NotNull(options);

        if (!options.ContextType.IsAssignableFrom(GetType()))
        {
            throw new InvalidOperationException(CoreStrings.NonGenericOptions(GetType().ShortDisplayName()));
        }

        _options = options;

        // This service is not stored in _setInitializer as this may not be the service provider that will be used
        // as the internal service provider going forward, because at this time OnConfiguring has not yet been called.
        // Mostly that isn't a problem because set initialization is done by our internal services, but in the case
        // where some of those services are replaced, this could initialize set using non-replaced services.
        // In this rare case if this is a problem for the app, then the app can just not use this mechanism to create
        // DbSet instances, and this code becomes a no-op. However, if this set initializer is then saved and used later
        // for the Set method, then it makes the problem bigger because now an app is using the non-replaced services
        // even when it doesn't need to.
        ServiceProviderCache.Instance.GetOrAdd(options, providerRequired: false)
            .GetRequiredService<IDbSetInitializer>()
            .InitializeSets(this);

        EntityFrameworkMetricsData.ReportDbContextInitializing();
    }