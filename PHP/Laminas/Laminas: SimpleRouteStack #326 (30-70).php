
    /**
     * Route plugin manager
     *
     * @var RoutePluginManager<TRoute>
     */
    protected $routePluginManager;

    /**
     * Default parameters.
     *
     * @var array
     */
    protected $defaultParams = [];

    /**
     * @param RoutePluginManager<TRoute>|null $routePluginManager
     */
    public function __construct(?RoutePluginManager $routePluginManager = null)
    {
        /** @var PriorityList<string, TRoute> $this->routes */
        $this->routes = new PriorityList();
        /** @var RoutePluginManager<TRoute> $this->routePluginManager */
        $this->routePluginManager = $routePluginManager ?? new RoutePluginManager(new ServiceManager());

        $this->init();
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::factory()
     *
     * @param  iterable $options
     * @return SimpleRouteStack
     * @throws Exception\InvalidArgumentException
     */
    public static function factory($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);