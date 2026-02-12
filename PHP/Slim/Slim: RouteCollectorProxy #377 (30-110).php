
    /** @var TContainerInterface */
    protected ?ContainerInterface $container = null;

    protected RouteCollectorInterface $routeCollector;

    protected string $groupPattern;

    /**
     * @param TContainerInterface $container
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        CallableResolverInterface $callableResolver,
        ?ContainerInterface $container = null,
        ?RouteCollectorInterface $routeCollector = null,
        string $groupPattern = ''
    ) {
        $this->responseFactory = $responseFactory;
        $this->callableResolver = $callableResolver;
        $this->container = $container;
        $this->routeCollector = $routeCollector ?? new RouteCollector($responseFactory, $callableResolver, $container);
        $this->groupPattern = $groupPattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallableResolver(): CallableResolverInterface
    {
        return $this->callableResolver;
    }

    /**
     * {@inheritdoc}
     * @return TContainerInterface
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollector(): RouteCollectorInterface
    {
        return $this->routeCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePath(): string
    {
        return $this->routeCollector->getBasePath();
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePath(string $basePath): RouteCollectorProxyInterface
    {
        $this->routeCollector->setBasePath($basePath);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $pattern, $callable): RouteInterface
    {