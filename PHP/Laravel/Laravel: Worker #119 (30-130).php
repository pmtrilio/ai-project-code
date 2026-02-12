    const EXIT_MEMORY_LIMIT = 12;

    /**
     * The name of the worker.
     *
     * @var string|null
     */
    protected $name;

    /**
     * The queue manager instance.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $manager;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The exception handler instance.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptions;

    /**
     * The callback used to determine if the application is in maintenance mode.
     *
     * @var callable
     */
    protected $isDownForMaintenance;

    /**
     * The callback used to reset the application's scope.
     *
     * @var callable
     */
    protected $resetScope;

    /**
     * Indicates if the worker should exit.
     *
     * @var bool
     */
    public $shouldQuit = false;

    /**
     * Indicates if the worker is paused.
     *
     * @var bool
     */
    public $paused = false;

    /**
     * The callbacks used to pop jobs from queues.
     *
     * @var callable[]
     */
    protected static $popCallbacks = [];

    /**
     * The custom exit code to be used when memory is exceeded.
     *
     * @var int|null
     */
    public static $memoryExceededExitCode;

    /**
     * Indicates if the worker should check for the restart signal in the cache.
     *
     * @var bool
     */
    public static $restartable = true;

    /**
     * Indicates if the worker should check for the paused signal in the cache.
     *
     * @var bool
     */
    public static $pausable = true;

    /**
     * Create a new queue worker.
     *
     * @param  \Illuminate\Contracts\Queue\Factory  $manager
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Contracts\Debug\ExceptionHandler  $exceptions
     * @param  callable  $isDownForMaintenance
     * @param  callable|null  $resetScope