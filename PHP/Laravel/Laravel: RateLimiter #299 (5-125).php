use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;

use function Illuminate\Support\enum_value;

class RateLimiter
{
    use InteractsWithTime;

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The configured limit object resolvers.
     *
     * @var array
     */
    protected $limiters = [];

    /**
     * Create a new rate limiter instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Register a named limiter configuration.
     *
     * @param  \BackedEnum|\UnitEnum|string  $name
     * @param  \Closure  $callback
     * @return $this
     */
    public function for($name, Closure $callback)
    {
        $resolvedName = $this->resolveLimiterName($name);

        $this->limiters[$resolvedName] = $callback;

        return $this;
    }

    /**
     * Get the given named rate limiter.
     *
     * @param  \BackedEnum|\UnitEnum|string  $name
     * @return \Closure|null
     */
    public function limiter($name)
    {
        $resolvedName = $this->resolveLimiterName($name);

        $limiter = $this->limiters[$resolvedName] ?? null;

        if (! is_callable($limiter)) {
            return;
        }

        return function (...$args) use ($limiter) {
            $result = $limiter(...$args);

            if (! is_array($result)) {
                return $result;
            }

            $duplicates = (new Collection($result))->duplicates('key');

            if ($duplicates->isEmpty()) {
                return $result;
            }

            foreach ($result as $limit) {
                if ($duplicates->contains($limit->key)) {
                    $limit->key = $limit->fallbackKey();
                }
            }

            return $result;
        };
    }

    /**
     * Attempts to execute a callback if it's not limited.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @param  \Closure  $callback
     * @param  \DateTimeInterface|\DateInterval|int  $decaySeconds
     * @return mixed
     */
    public function attempt($key, $maxAttempts, Closure $callback, $decaySeconds = 60)
    {
        if ($this->tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        if (is_null($result = $callback())) {
            $result = true;
        }

        return tap($result, function () use ($key, $decaySeconds) {
            $this->hit($key, $decaySeconds);
        });
    }

    /**
     * Determine if the given key has been "accessed" too many times.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return bool