///     doing so can result in application failures when updating to a new Entity Framework Core release.
/// </summary>
public class DbContextOperations
{
    private readonly IOperationReporter _reporter;
    private readonly Assembly _assembly;
    private readonly Assembly _startupAssembly;
    private readonly string _project;
    private readonly string _projectDir;
    private readonly string? _rootNamespace;
    private readonly string? _language;
    private readonly bool _nullable;
    private readonly string[] _args;
    private readonly AppServiceProviderFactory _appServicesFactory;
    private readonly DesignTimeServicesBuilder _servicesBuilder;

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public DbContextOperations(
        IOperationReporter reporter,
        Assembly assembly,
        Assembly startupAssembly,
        string project,
        string projectDir,
        string? rootNamespace,
        string? language,
        bool nullable,
        string[]? args)
        : this(
            reporter,
            assembly,
            startupAssembly,
            project,
            projectDir,
            rootNamespace,
            language,
            nullable,
            args,
            new AppServiceProviderFactory(startupAssembly, reporter))
    {
    }

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    protected DbContextOperations(
        IOperationReporter reporter,
        Assembly assembly,
        Assembly startupAssembly,
        string project,
        string projectDir,
        string? rootNamespace,
        string? language,
        bool nullable,