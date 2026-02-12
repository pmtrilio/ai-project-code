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