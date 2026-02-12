///     the same compatibility standards as public APIs. It may be changed or removed without notice in
///     any release. You should only use it directly in your code with extreme caution and knowing that
///     doing so can result in application failures when updating to a new Entity Framework Core release.
/// </summary>
public class QueryCompiler : IQueryCompiler
{
    private readonly IQueryContextFactory _queryContextFactory;
    private readonly ICompiledQueryCache _compiledQueryCache;
    private readonly ICompiledQueryCacheKeyGenerator _compiledQueryCacheKeyGenerator;
    private readonly IDatabase _database;
    private readonly IDiagnosticsLogger<DbLoggerCategory.Query> _logger;

    private readonly Type _contextType;
    private readonly IEvaluatableExpressionFilter _evaluatableExpressionFilter;
    private readonly IModel _model;

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public QueryCompiler(
        IQueryContextFactory queryContextFactory,
        ICompiledQueryCache compiledQueryCache,
        ICompiledQueryCacheKeyGenerator compiledQueryCacheKeyGenerator,
        IDatabase database,
        IDiagnosticsLogger<DbLoggerCategory.Query> logger,
        ICurrentDbContext currentContext,
        IEvaluatableExpressionFilter evaluatableExpressionFilter,
        IModel model)
    {
        _queryContextFactory = queryContextFactory;
        _compiledQueryCache = compiledQueryCache;
        _compiledQueryCacheKeyGenerator = compiledQueryCacheKeyGenerator;
        _database = database;
        _logger = logger;
        _contextType = currentContext.Context.GetType();
        _evaluatableExpressionFilter = evaluatableExpressionFilter;
        _model = model;
    }