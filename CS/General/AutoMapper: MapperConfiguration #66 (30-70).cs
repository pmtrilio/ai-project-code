    /// </summary>
    /// <param name="sourceType">the runtime type of the source object</param>
    /// <param name="destinationType">the runtime type of the destination object</param>
    /// <returns>the execution plan</returns>
    LambdaExpression BuildExecutionPlan(Type sourceType, Type destinationType);
    /// <summary>
    /// Compile all underlying mapping expressions to cached delegates.
    /// Use if you want AutoMapper to compile all mappings up front instead of deferring expression compilation for each first map.
    /// </summary>
    void CompileMappings();
}
public sealed class MapperConfiguration : IGlobalConfiguration
{
    private static readonly MethodInfo MappingError = typeof(MapperConfiguration).GetMethod(nameof(GetMappingError));
    private readonly IObjectMapper[] _mappers;
    private readonly Dictionary<TypePair, TypeMap> _configuredMaps;
    private readonly Dictionary<TypePair, TypeMap> _resolvedMaps;
    private readonly LockingConcurrentDictionary<TypePair, TypeMap> _runtimeMaps;
    private LazyValue<ProjectionBuilder> _projectionBuilder;
    private readonly LockingConcurrentDictionary<MapRequest, Delegate> _executionPlans;
    private readonly MapperConfigurationExpression _configurationExpression;
    private readonly ILoggerFactory _loggerFactory;
    private readonly Features<IRuntimeFeature> _features = new();
    private readonly bool _hasOpenMaps;
    private readonly HashSet<TypeMap> _typeMapsPath = [];
    private readonly List<MemberInfo> _sourceMembers = [];
    private readonly List<ParameterExpression> _variables = [];
    private readonly ParameterExpression[] _parameters = [null, null, ContextParameter];
    private readonly CatchBlock[] _catches = [null];
    private readonly List<Expression> _expressions = [];
    private readonly Dictionary<Type, DefaultExpression> _defaults;
    private readonly ParameterReplaceVisitor _parameterReplaceVisitor = new();
    private readonly ConvertParameterReplaceVisitor _convertParameterReplaceVisitor = new();
    private readonly List<Type> _typesInheritance = [];
    private readonly LicenseAccessor _licenseAccessor;

    public MapperConfiguration(MapperConfigurationExpression configurationExpression, ILoggerFactory loggerFactory)
    {
        _configurationExpression = configurationExpression;
        _loggerFactory = loggerFactory;
        var configuration = (IGlobalConfigurationExpression)configurationExpression;