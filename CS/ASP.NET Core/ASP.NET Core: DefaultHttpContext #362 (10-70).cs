using Microsoft.AspNetCore.Shared;
using Microsoft.AspNetCore.WebUtilities;
using Microsoft.Extensions.DependencyInjection;

namespace Microsoft.AspNetCore.Http;

/// <summary>
/// Represents an implementation of the HTTP Context class.
/// </summary>
// DebuggerDisplayAttribute is inherited but we're replacing it on this implementation to include reason phrase.
[DebuggerDisplay("{DebuggerToString(),nq}")]
public sealed class DefaultHttpContext : HttpContext
{
    // The initial size of the feature collection when using the default constructor; based on number of common features
    // https://github.com/dotnet/aspnetcore/issues/31249
    private const int DefaultFeatureCollectionSize = 10;

    // Lambdas hoisted to static readonly fields to improve inlining https://github.com/dotnet/roslyn/issues/13624
    private static readonly Func<IFeatureCollection, IItemsFeature> _newItemsFeature = f => new ItemsFeature();
    private static readonly Func<DefaultHttpContext, IServiceProvidersFeature> _newServiceProvidersFeature = context => new RequestServicesFeature(context, context.ServiceScopeFactory);
    private static readonly Func<IFeatureCollection, IHttpAuthenticationFeature> _newHttpAuthenticationFeature = f => new HttpAuthenticationFeature();
    private static readonly Func<IFeatureCollection, IHttpRequestLifetimeFeature> _newHttpRequestLifetimeFeature = f => new HttpRequestLifetimeFeature();
    private static readonly Func<IFeatureCollection, ISessionFeature> _newSessionFeature = f => new DefaultSessionFeature();
    private static readonly Func<IFeatureCollection, ISessionFeature?> _nullSessionFeature = f => null;
    private static readonly Func<IFeatureCollection, IHttpRequestIdentifierFeature> _newHttpRequestIdentifierFeature = f => new HttpRequestIdentifierFeature();

    private FeatureReferences<FeatureInterfaces> _features;

    private readonly DefaultHttpRequest _request;
    private readonly DefaultHttpResponse _response;

    private DefaultConnectionInfo? _connection;
    private DefaultWebSocketManager? _websockets;

    // This is field exists to make analyzing memory dumps easier.
    // https://github.com/dotnet/aspnetcore/issues/29709
    internal bool _active;

    /// <summary>
    /// Initializes a new instance of the <see cref="DefaultHttpContext"/> class.
    /// </summary>
    public DefaultHttpContext()
        : this(new FeatureCollection(DefaultFeatureCollectionSize))
    {
        Features.Set<IHttpRequestFeature>(new HttpRequestFeature());
        Features.Set<IHttpResponseFeature>(new HttpResponseFeature());
        Features.Set<IHttpResponseBodyFeature>(new StreamResponseBodyFeature(Stream.Null));
    }

    /// <summary>
    /// Initializes a new instance of the <see cref="DefaultHttpContext"/> class with provided features.
    /// </summary>
    /// <param name="features">Initial set of features for the <see cref="DefaultHttpContext"/>.</param>
    public DefaultHttpContext(IFeatureCollection features)
    {
        _features.Initalize(features);
        _request = new DefaultHttpRequest(this);
        _response = new DefaultHttpResponse(this);
    }

    /// <summary>