class Route
{
    use Conditionable, CreatesRegularExpressionRouteConstraints, FiltersControllerMiddleware, Macroable, ResolvesRouteDependencies;

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The HTTP methods the route responds to.
     *
     * @var array
     */
    public $methods;

    /**
     * The route action array.
     *
     * @var array
     */
    public $action;

    /**
     * Indicates whether the route is a fallback route.
     *
     * @var bool
     */
    public $isFallback = false;

    /**
     * The controller instance.
     *
     * @var mixed
     */
    public $controller;

    /**
     * The default values for the route.
     *
     * @var array
     */
    public $defaults = [];

    /**
     * The regular expression requirements.
     *
     * @var array
     */
    public $wheres = [];

    /**
     * The array of matched parameters.
     *
     * @var array|null
     */
    public $parameters;

    /**
     * The parameter names for the route.
     *
     * @var array|null
     */
    public $parameterNames;

    /**
     * The array of the matched parameters' original values.
     *
     * @var array
     */
    protected $originalParameters;

    /**
     * Indicates "trashed" models can be retrieved when resolving implicit model bindings for this route.
     *
     * @var bool
     */
    protected $withTrashedBindings = false;

    /**
     * Indicates the maximum number of seconds the route should acquire a session lock for.
     *
     * @var int|null
     */
    protected $lockSeconds;

    /**
     * Indicates the maximum number of seconds the route should wait while attempting to acquire a session lock.
     *
     * @var int|null
     */
    protected $waitSeconds;

    /**
     * The computed gathered middleware.
     *
     * @var array|null
     */
    public $computedMiddleware;