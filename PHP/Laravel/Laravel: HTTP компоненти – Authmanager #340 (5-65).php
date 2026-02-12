use Closure;
use Illuminate\Contracts\Auth\Factory as FactoryContract;
use InvalidArgumentException;

/**
 * @mixin \Illuminate\Contracts\Auth\Guard
 * @mixin \Illuminate\Contracts\Auth\StatefulGuard
 */
class AuthManager implements FactoryContract
{
    use CreatesUserProviders;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $guards = [];

    /**
     * The user resolver shared by various services.
     *
     * Determines the default user for Gate, Request, and the Authenticatable contract.
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * Create a new Auth manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct($app)
    {
        $this->app = $app;

        $this->userResolver = fn ($guard = null) => $this->guard($guard)->user();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard($name = null)