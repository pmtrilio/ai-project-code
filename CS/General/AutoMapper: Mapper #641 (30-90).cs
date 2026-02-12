    /// <returns>The mapped destination object</returns>
    TDestination Map<TSource, TDestination>(TSource source, TDestination destination);
    /// <summary>
    /// Execute a mapping from the source object to a new destination object with explicit <see cref="System.Type"/> objects
    /// </summary>
    /// <param name="source">Source object to map from</param>
    /// <param name="sourceType">Source type to use</param>
    /// <param name="destinationType">Destination type to create</param>
    /// <returns>Mapped destination object</returns>
    object Map(object source, Type sourceType, Type destinationType);
    /// <summary>
    /// Execute a mapping from the source object to existing destination object with explicit <see cref="System.Type"/> objects
    /// </summary>
    /// <param name="source">Source object to map from</param>
    /// <param name="destination">Destination object to map into</param>
    /// <param name="sourceType">Source type to use</param>
    /// <param name="destinationType">Destination type to use</param>
    /// <returns>The mapped destination object</returns>
    object Map(object source, object destination, Type sourceType, Type destinationType);
}
public interface IMapper : IMapperBase
{
    /// <summary>
    /// Execute a mapping from the source object to a new destination object with supplied mapping options.
    /// </summary>
    /// <typeparam name="TDestination">Destination type to create</typeparam>
    /// <param name="source">Source object to map from</param>
    /// <param name="opts">Mapping options</param>
    /// <returns>Mapped destination object</returns>
    TDestination Map<TDestination>(object source, Action<IMappingOperationOptions<object, TDestination>> opts);
    /// <summary>
    /// Execute a mapping from the source object to a new destination object with supplied mapping options.
    /// </summary>
    /// <typeparam name="TSource">Source type to use</typeparam>
    /// <typeparam name="TDestination">Destination type to create</typeparam>
    /// <param name="source">Source object to map from</param>
    /// <param name="opts">Mapping options</param>
    /// <returns>Mapped destination object</returns>
    TDestination Map<TSource, TDestination>(TSource source, Action<IMappingOperationOptions<TSource, TDestination>> opts);
    /// <summary>
    /// Execute a mapping from the source object to the existing destination object with supplied mapping options.
    /// </summary>
    /// <typeparam name="TSource">Source type to use</typeparam>
    /// <typeparam name="TDestination">Destination type</typeparam>
    /// <param name="source">Source object to map from</param>
    /// <param name="destination">Destination object to map into</param>
    /// <param name="opts">Mapping options</param>
    /// <returns>The mapped destination object</returns>
    TDestination Map<TSource, TDestination>(TSource source, TDestination destination, Action<IMappingOperationOptions<TSource, TDestination>> opts);
    /// <summary>
    /// Execute a mapping from the source object to a new destination object with explicit <see cref="System.Type"/> objects and supplied mapping options.
    /// </summary>
    /// <param name="source">Source object to map from</param>
    /// <param name="sourceType">Source type to use</param>
    /// <param name="destinationType">Destination type to create</param>
    /// <param name="opts">Mapping options</param>
    /// <returns>Mapped destination object</returns>
    object Map(object source, Type sourceType, Type destinationType, Action<IObjectMappingOperationOptions> opts);
    /// <summary>
    /// Execute a mapping from the source object to existing destination object with supplied mapping options and explicit <see cref="System.Type"/> objects
    /// </summary>