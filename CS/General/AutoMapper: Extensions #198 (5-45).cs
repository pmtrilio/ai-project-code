/// <summary>
/// Queryable extensions for AutoMapper
/// </summary>
public static class Extensions
{
    static readonly MethodInfo SelectMethod = typeof(Queryable).StaticGenericMethod("Select", parametersCount: 2);
    static IQueryable Select(IQueryable source, LambdaExpression lambda) => source.Provider.CreateQuery(
        Call(SelectMethod.MakeGenericMethod(source.ElementType, lambda.ReturnType), source.Expression, Quote(lambda)));
    /// <summary>
    /// Extension method to project from a queryable using the provided mapping engine
    /// </summary>
    /// <remarks>Projections are only calculated once and cached</remarks>
    /// <typeparam name="TDestination">Destination type</typeparam>
    /// <param name="source">Queryable source</param>
    /// <param name="configuration">Mapper configuration</param>
    /// <param name="parameters">Optional parameter object for parameterized mapping expressions</param>
    /// <param name="membersToExpand">Explicit members to expand</param>
    /// <returns>Expression to project into</returns>
    public static IQueryable<TDestination> ProjectTo<TDestination>(this IQueryable source, IConfigurationProvider configuration, object parameters, params Expression<Func<TDestination, object>>[] membersToExpand) =>
        source.ToCore<TDestination>(configuration, parameters, membersToExpand.Select(MemberVisitor.GetMemberPath));
    /// <summary>
    /// Extension method to project from a queryable using the provided mapping engine
    /// </summary>
    /// <remarks>Projections are only calculated once and cached</remarks>
    /// <typeparam name="TDestination">Destination type</typeparam>
    /// <param name="source">Queryable source</param>
    /// <param name="configuration">Mapper configuration</param>
    /// <param name="membersToExpand">Explicit members to expand</param>
    /// <returns>Expression to project into</returns>
    public static IQueryable<TDestination> ProjectTo<TDestination>(this IQueryable source, IConfigurationProvider configuration,
        params Expression<Func<TDestination, object>>[] membersToExpand) => 
        source.ProjectTo(configuration, null, membersToExpand);
    /// <summary>
    /// Projects the source type to the destination type given the mapping configuration
    /// </summary>
    /// <typeparam name="TDestination">Destination type to map to</typeparam>
    /// <param name="source">Queryable source</param>
    /// <param name="configuration">Mapper configuration</param>
    /// <param name="parameters">Optional parameter object for parameterized mapping expressions</param>
    /// <param name="membersToExpand">Explicit members to expand</param>
    /// <returns>Queryable result, use queryable extension methods to project and execute result</returns>