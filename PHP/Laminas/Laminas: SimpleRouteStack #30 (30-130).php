
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
        } elseif (! is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of options',
                __METHOD__
            ));
        }

        $routePluginManager = null;
        if (isset($options['route_plugins'])) {
            $routePluginManager = $options['route_plugins'];
        }

        $instance = new static($routePluginManager);

        if (isset($options['routes'])) {
            $instance->addRoutes($options['routes']);
        }

        if (isset($options['default_params'])) {
            $instance->setDefaultParams($options['default_params']);
        }

        return $instance;
    }

    /**
     * Init method for extending classes.
     *
     * @return void
     */
    protected function init()
    {
    }

    /**
     * @param RoutePluginManager<TRoute> $routePlugins
     * @return $this
     */
    public function setRoutePluginManager(RoutePluginManager $routePlugins)
    {
        $this->routePluginManager = $routePlugins;
        return $this;
    }

    /**
     * Get the route plugin manager.
     *
     * @return RoutePluginManager<TRoute>
     */
    public function getRoutePluginManager()
    {
        return $this->routePluginManager;
    }

    /** @inheritDoc */
    public function addRoutes($routes)
    {
        if (! is_array($routes) && ! $routes instanceof Traversable) {
            throw new Exception\InvalidArgumentException('addRoutes expects an array or Traversable set of routes');
        }