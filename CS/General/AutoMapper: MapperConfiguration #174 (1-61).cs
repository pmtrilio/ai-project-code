using AutoMapper.Licensing;
using Microsoft.Extensions.Logging;
using Microsoft.Extensions.Logging.Abstractions;

namespace AutoMapper;
using Features;
using Internal.Mappers;
using QueryableExtensions.Impl;
public interface IConfigurationProvider
{
    /// <summary>
    /// Dry run all configured type maps and throw <see cref="AutoMapperConfigurationException"/> for each problem
    /// </summary>
    void AssertConfigurationIsValid();
    /// <summary>
    /// Create a mapper instance based on this configuration. Mapper instances are lightweight and can be created as needed.
    /// </summary>
    /// <returns>The mapper instance</returns>
    IMapper CreateMapper();
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