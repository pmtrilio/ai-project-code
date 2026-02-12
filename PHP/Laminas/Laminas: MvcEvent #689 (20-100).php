    public const EVENT_DISPATCH_ERROR = 'dispatch.error';
    public const EVENT_FINISH         = 'finish';
    public const EVENT_RENDER         = 'render';
    public const EVENT_RENDER_ERROR   = 'render.error';
    public const EVENT_ROUTE          = 'route';
    /** @var ApplicationInterface|null */
    protected $application;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var mixed */
    protected $result;

    /** @var RouteStackInterface */
    protected $router;

    /** @var null|RouteMatch */
    protected $routeMatch;

    /** @var Model */
    protected $viewModel;

    /**
     * Set application instance
     *
     * @return MvcEvent
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->setParam('application', $application);
        $this->application = $application;
        return $this;
    }

    /**
     * Get application instance
     *
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Get router
     *
     * @return RouteStackInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Set router
     *
     * @return MvcEvent
     */
    public function setRouter(RouteStackInterface $router)
    {
        $this->setParam('router', $router);
        $this->router = $router;
        return $this;
    }

    /**
     * Get route match
     *
     * @return null|RouteMatch
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**