use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use ReflectionClass;

use function Illuminate\Support\enum_value;

class Dispatcher implements DispatcherContract
{
    use Macroable, ReflectsClosures, ResolvesQueueRoutes;

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
    public function listen($events, $listener = null)
    {
        if ($events instanceof Closure) {
            return (new Collection($this->firstClosureParameterTypes($events)))
                ->each(function ($event) use ($events) {
                    $this->listen($event, $events);
                });
        } elseif ($events instanceof QueuedClosure) {
            return (new Collection($this->firstClosureParameterTypes($events->closure)))
                ->each(function ($event) use ($events) {