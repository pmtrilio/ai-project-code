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
    /// Reinitialize  the current instant of the class with features passed in.
    /// </summary>
    /// <remarks>
    /// This method allows the consumer to re-use the <see cref="DefaultHttpContext" /> for another request, rather than having to allocate a new instance.
    /// </remarks>
    /// <param name="features">The new set of features for the <see cref="DefaultHttpContext" />.</param>
    public void Initialize(IFeatureCollection features)
    {
        var revision = features.Revision;
        _features.Initalize(features, revision);
        _request.Initialize(revision);
        _response.Initialize(revision);
        _connection?.Initialize(features, revision);
        _websockets?.Initialize(features, revision);
        _active = true;
    }

    /// <summary>
    /// Uninitialize all the features in the <see cref="DefaultHttpContext" />.
    /// </summary>
    public void Uninitialize()
    {
        _features = default;
        _request.Uninitialize();
        _response.Uninitialize();
        _connection?.Uninitialize();
        _websockets?.Uninitialize();
        _active = false;
    }

    /// <summary>
    /// Gets or set the <see cref="FormOptions" /> for this instance.
    /// </summary>
    /// <returns>
    /// <see cref="FormOptions"/>
    /// </returns>
    public FormOptions FormOptions { get; set; } = default!;

    /// <summary>
    /// Gets or sets the <see cref="IServiceScopeFactory" /> for this instance.