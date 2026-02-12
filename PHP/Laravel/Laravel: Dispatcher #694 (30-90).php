     * The pipeline instance for the bus.
     *
     * @var \Illuminate\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * The pipes to send commands through before dispatching.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The command to handler mapping for non-self-handling events.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * The queue resolver callback.
     *
     * @var \Closure|null
     */
    protected $queueResolver;

    /**
     * Indicates if dispatching after response is disabled.
     *
     * @var bool
     */
    protected $allowsDispatchingAfterResponses = true;

    /**
     * Create a new command dispatcher instance.
     */
    public function __construct(Container $container, ?Closure $queueResolver = null)
    {
        $this->container = $container;
        $this->queueResolver = $queueResolver;
        $this->pipeline = new Pipeline($container);
    }

    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatch($command)
    {
        return $this->queueResolver && $this->commandShouldBeQueued($command)
            ? $this->dispatchToQueue($command)
            : $this->dispatchNow($command);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.