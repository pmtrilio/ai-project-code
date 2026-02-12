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