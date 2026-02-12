    protected static ?Psr17FactoryProviderInterface $psr17FactoryProvider = null;

    protected static ?ResponseFactoryInterface $responseFactory = null;

    protected static ?StreamFactoryInterface $streamFactory = null;

    protected static ?ContainerInterface $container = null;

    protected static ?CallableResolverInterface $callableResolver = null;

    protected static ?RouteCollectorInterface $routeCollector = null;

    protected static ?RouteResolverInterface $routeResolver = null;

    protected static ?MiddlewareDispatcherInterface $middlewareDispatcher = null;

    protected static bool $slimHttpDecoratorsAutomaticDetectionEnabled = true;

    /**
     * @template TContainerInterface of (ContainerInterface|null)
     * @param TContainerInterface $container
     * @return (TContainerInterface is ContainerInterface ? App<TContainerInterface> : App<ContainerInterface|null>)
     */
    public static function create(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ): App {
        static::$responseFactory = $responseFactory ?? static::$responseFactory;
        return new App(
            self::determineResponseFactory(),
            $container ?? static::$container,
            $callableResolver ?? static::$callableResolver,
            $routeCollector ?? static::$routeCollector,
            $routeResolver ?? static::$routeResolver,
            $middlewareDispatcher ?? static::$middlewareDispatcher
        );
    }

    /**
     * @template TContainerInterface of (ContainerInterface)
     * @param TContainerInterface $container
     * @return App<TContainerInterface>
     */
    public static function createFromContainer(ContainerInterface $container): App
    {
        $responseFactory = $container->has(ResponseFactoryInterface::class)
        && (
            $responseFactoryFromContainer = $container->get(ResponseFactoryInterface::class)
        ) instanceof ResponseFactoryInterface
            ? $responseFactoryFromContainer
            : self::determineResponseFactory();

        $callableResolver = $container->has(CallableResolverInterface::class)
        && (
            $callableResolverFromContainer = $container->get(CallableResolverInterface::class)
        ) instanceof CallableResolverInterface
            ? $callableResolverFromContainer
            : null;

        $routeCollector = $container->has(RouteCollectorInterface::class)
        && (
            $routeCollectorFromContainer = $container->get(RouteCollectorInterface::class)
        ) instanceof RouteCollectorInterface
            ? $routeCollectorFromContainer
            : null;

        $routeResolver = $container->has(RouteResolverInterface::class)
        && (
            $routeResolverFromContainer = $container->get(RouteResolverInterface::class)
        ) instanceof RouteResolverInterface
            ? $routeResolverFromContainer
            : null;

        $middlewareDispatcher = $container->has(MiddlewareDispatcherInterface::class)
        && (
            $middlewareDispatcherFromContainer = $container->get(MiddlewareDispatcherInterface::class)
        ) instanceof MiddlewareDispatcherInterface