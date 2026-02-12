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

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public virtual TResult Execute<TResult>(Expression query)
        => ExecuteCore<TResult>(query, async: false, CancellationToken.None);

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public virtual TResult ExecuteAsync<TResult>(Expression query, CancellationToken cancellationToken = default)
        => ExecuteCore<TResult>(query, async: true, cancellationToken);

    private TResult ExecuteCore<TResult>(Expression query, bool async, CancellationToken cancellationToken)
    {
        var queryContext = _queryContextFactory.Create();

        queryContext.CancellationToken = cancellationToken;

        var queryAfterExtraction = ExtractParameters(query, queryContext.Parameters, _logger);

        var compiledQuery
            = _compiledQueryCache
                .GetOrAddQuery(
                    _compiledQueryCacheKeyGenerator.GenerateCacheKey(queryAfterExtraction, async),
                    () => RuntimeFeature.IsDynamicCodeSupported
                        ? CompileQueryCore<TResult>(_database, queryAfterExtraction, _model, async)
                        : throw new InvalidOperationException(CoreStrings.QueryNotPrecompiled));

        return compiledQuery(queryContext);
    }

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public virtual Func<QueryContext, TResult> CreateCompiledQuery<TResult>(Expression query)
    {
        var queryContext = _queryContextFactory.Create();

        var queryAfterExtraction = ExtractParameters(query, queryContext.Parameters, _logger, compiledQuery: true);

        return CompileQueryCore<TResult>(_database, queryAfterExtraction, _model, false);
    }

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public virtual Func<QueryContext, TResult> CreateCompiledAsyncQuery<TResult>(Expression query)