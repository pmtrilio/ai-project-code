use Cake\Core\Exception\CakeException;
use Cake\Http\ServerRequest;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Route\Route;
use Closure;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use ReflectionFunction;
use Throwable;

/**
 * Parses the request URL into controller, action, and parameters. Uses the connected routes
 * to match the incoming URL string to parameters that will allow the request to be dispatched. Also
 * handles converting parameter lists into URL strings, using the connected routes. Routing allows you to decouple
 * the way the world interacts with your application (URLs) and the implementation (controllers and actions).
 */
class Router
{
    /**
     * Default route class.
     *
     * @var string
     */
    protected static string $_defaultRouteClass = Route::class;

    /**
     * Contains the base string that will be applied to all generated URLs
     * For example `https://example.com`
     *
     * @var string|null
     */
    protected static ?string $_fullBaseUrl = null;

    /**
     * Regular expression for action names
     *
     * @var string
     */
    public const ACTION = 'index|show|add|create|edit|update|remove|del|delete|view|item';

    /**