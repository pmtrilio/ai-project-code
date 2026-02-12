
    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The registered event listeners.
     *
     * @var array<string, callable|array|class-string|null>
     */
    protected $listeners = [];

    /**
     * The wildcard listeners.
     *
     * @var array<string, \Closure|string>
     */
    protected $wildcards = [];

    /**
     * The cached wildcard listeners.
     *
     * @var array<string, \Closure|string>
     */
    protected $wildcardsCache = [];

    /**
     * The queue resolver instance.
     *
     * @var callable(): \Illuminate\Contracts\Queue\Queue
     */
    protected $queueResolver;

    /**
     * The database transaction manager resolver instance.
     *
     * @var callable
     */
    protected $transactionManagerResolver;

    /**
     * The currently deferred events.
     *
     * @var array
     */
    protected $deferredEvents = [];

    /**
     * Indicates if events should be deferred.
     *
     * @var bool
     */
    protected $deferringEvents = false;

    /**
     * The specific events to defer (null means defer all events).
     *
     * @var string[]|null
     */
    protected $eventsToDefer = null;

    /**
     * Create a new event dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     */
    public function __construct(?ContainerContract $container = null)
    {
        $this->container = $container ?: new Container;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param  \Illuminate\Events\QueuedClosure|callable|array|class-string|string  $events
     * @param  \Illuminate\Events\QueuedClosure|callable|array|class-string|null  $listener
     * @return void
     */