///     Provides access to database related information and operations for a context.
///     Instances of this class are typically obtained from <see cref="DbContext.Database" /> and it is not designed
///     to be directly constructed in your application code.
/// </summary>
public class DatabaseFacade : IInfrastructure<IServiceProvider>, IDatabaseFacadeDependenciesAccessor, IResettableService
{
    private readonly DbContext _context;

    /// <summary>
    ///     Initializes a new instance of the <see cref="DatabaseFacade" /> class. Instances of this class are typically
    ///     obtained from <see cref="DbContext.Database" /> and it is not designed to be directly constructed
    ///     in your application code.
    /// </summary>
    /// <param name="context">The context this database API belongs to.</param>
    public DatabaseFacade(DbContext context)
        => _context = context;

    [field: AllowNull, MaybeNull]
    private IDatabaseFacadeDependencies Dependencies
        => field ??= _context.GetService<IDatabaseFacadeDependencies>();

    /// <summary>
    ///     Ensures that the database for the context exists.
    /// </summary>
    /// <remarks>
    ///     <list type="bullet">
    ///         <item>
    ///             <description>
    ///                 If the database exists and has any tables, then no action is taken. Nothing is done to ensure
    ///                 the database schema is compatible with the Entity Framework model.
    ///             </description>
    ///         </item>
    ///         <item>
    ///             <description>
    ///                 If the database exists but does not have any tables, then the Entity Framework model is used to
    ///                 create the database schema.
    ///             </description>
    ///         </item>
    ///         <item>
    ///             <description>
    ///                 If the database does not exist, then the database is created and the Entity Framework model is used to
    ///                 create the database schema.
    ///             </description>
    ///         </item>
    ///     </list>
    ///     <para>
    ///         It is common to use <see cref="EnsureCreated" /> immediately following <see cref="EnsureDeleted" /> when
    ///         testing or prototyping using Entity Framework. This ensures that the database is in a clean state before each
    ///         execution of the test/prototype. Note, however, that data in the database is not preserved.
    ///     </para>
    ///     <para>
    ///         Note that this API does **not** use migrations to create the database. In addition, the database that is
    ///         created cannot be later updated using migrations. If you are targeting a relational database and using migrations,
    ///         then you can use <see cref="M:Microsoft.EntityFrameworkCore.RelationalDatabaseFacadeExtensions.Migrate" />
    ///         to ensure the database is created using migrations and that all migrations have been applied.
    ///     </para>
    ///     <para>
    ///         See <see href="https://aka.ms/efcore-docs-manage-schemas">Managing database schemas with EF Core</see>
    ///         and <see href="https://aka.ms/efcore-docs-ensure-created">Database creation APIs</see> for more information and examples.
    ///     </para>
    /// </remarks>
    /// <returns><see langword="true" /> if the database is created, <see langword="false" /> if it already existed.</returns>
    [RequiresDynamicCode(
        "Migrations operations require building the design-time model which is not supported with NativeAOT"
        + " Use a migration bundle or an alternate way of executing migration operations.")]
    public virtual bool EnsureCreated()
        => Dependencies.DatabaseCreator.EnsureCreated();

    /// <summary>
    ///     Ensures that the database for the context exists.
    /// </summary>
    /// <remarks>
    ///     <list type="bullet">
    ///         <item>
    ///             <description>
    ///                 If the database exists and has any tables, then no action is taken. Nothing is done to ensure
    ///                 the database schema is compatible with the Entity Framework model.
    ///             </description>
    ///         </item>
    ///         <item>
    ///             <description>
    ///                 If the database exists but does not have any tables, then the Entity Framework model is used to
    ///                 create the database schema.
    ///             </description>
    ///         </item>
    ///         <item>
    ///             <description>
    ///                 If the database does not exist, then the database is created and the Entity Framework model is used to
    ///                 create the database schema.
    ///             </description>
    ///         </item>
    ///     </list>
    ///     <para>
    ///         It is common to use <see cref="EnsureCreatedAsync" /> immediately following <see cref="EnsureDeletedAsync" /> when
    ///         testing or prototyping using Entity Framework. This ensures that the database is in a clean state before each
    ///         execution of the test/prototype. Note, however, that data in the database is not preserved.
    ///     </para>
    ///     <para>
    ///         Note that this API does **not** use migrations to create the database. In addition, the database that is
    ///         created cannot be later updated using migrations. If you are targeting a relational database and using migrations,
    ///         then you can use <see cref="M:Microsoft.EntityFrameworkCore.RelationalDatabaseFacadeExtensions.MigrateAsync" />