
use function Illuminate\Support\defer;
use function Illuminate\Support\enum_value;

/**
 * @mixin \Illuminate\Contracts\Cache\Store
 */
class Repository implements ArrayAccess, CacheContract
{
    use InteractsWithTime, Macroable {
        __call as macroCall;
    }

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Store
     */
    protected $store;

    /**
     * The event dispatcher implementation.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The default number of seconds to store items.
     *
     * @var int|null
     */
    protected $default = 3600;

    /**
     * The cache store configuration options.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create a new cache repository instance.
     *
     * @param  \Illuminate\Contracts\Cache\Store  $store
     * @param  array  $config
     */
    public function __construct(Store $store, array $config = [])
    {
        $this->store = $store;
        $this->config = $config;
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param  \BackedEnum|\UnitEnum|array|string  $key
     * @return bool
     */
    public function has($key): bool
    {