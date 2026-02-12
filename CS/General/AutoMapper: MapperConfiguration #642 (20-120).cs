    /// <summary>
    /// Create a mapper instance with the specified service constructor to be used for resolvers and type converters.
    /// </summary>
    /// <param name="serviceCtor">Service factory to create services</param>
    /// <returns>The mapper instance</returns>
    IMapper CreateMapper(Func<Type, object> serviceCtor);
    /// <summary>
    /// Builds the execution plan used to map the source to destination.
    /// Useful to understand what exactly is happening during mapping.
    /// See <a href="https://automapper.readthedocs.io/en/latest/Understanding-your-mapping.html">the wiki</a> for details.
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
        if (configuration.MethodMappingEnabled != false)
        {
            configuration.IncludeSourceExtensionMethods(typeof(Enumerable));
        }
        _mappers = [..configuration.Mappers];
        _executionPlans = new(CompileExecutionPlan);
        _projectionBuilder = new(CreateProjectionBuilder);
        Configuration = new((IProfileConfiguration)configuration);
        int typeMapsCount = Configuration.TypeMapsCount;
        int openTypeMapsCount = Configuration.OpenTypeMapsCount;
        Profiles = new ProfileMap[configuration.Profiles.Count + 1];
        Profiles[0] = Configuration;
        int index = 1;
        foreach (var profile in configuration.Profiles)
        {
            ProfileMap profileMap = new(profile, configuration);
            Profiles[index++] = profileMap;
            typeMapsCount += profileMap.TypeMapsCount;
            openTypeMapsCount += profileMap.OpenTypeMapsCount;
        }
        _defaults = new(3 * typeMapsCount);
        _configuredMaps = new(typeMapsCount);
        _hasOpenMaps = openTypeMapsCount > 0;
        _resolvedMaps = new(2 * typeMapsCount);
        configuration.Features.Configure(this);
        _licenseAccessor = new LicenseAccessor(this, _loggerFactory);

        Seal();

        foreach (var profile in Profiles)
        {
            profile.Clear();
        }
        _configuredMaps.TrimExcess();
        _resolvedMaps.TrimExcess();
        _typeMapsPath = null;
        _sourceMembers = null;
        _expressions = null;
        _variables = null;
        _parameters = null;
        _catches = null;
        _defaults = null;
        _convertParameterReplaceVisitor = null;
        _parameterReplaceVisitor = null;
        _typesInheritance = null;
        _runtimeMaps = new(GetTypeMap, openTypeMapsCount);

        var validator = new LicenseValidator(loggerFactory);
        validator.Validate(_licenseAccessor.Current);
        