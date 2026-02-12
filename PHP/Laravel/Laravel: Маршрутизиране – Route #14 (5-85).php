use BackedEnum;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Contracts\CallableDispatcher;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Matching\HostValidator;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Matching\SchemeValidator;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use LogicException;
use Symfony\Component\Routing\Route as SymfonyRoute;

use function Illuminate\Support\enum_value;

class Route
{
    use Conditionable, CreatesRegularExpressionRouteConstraints, FiltersControllerMiddleware, Macroable, ResolvesRouteDependencies;

    /**
     * The URI pattern the route responds to.
     *
     * @var string
     */
    public $uri;

    /**
     * The HTTP methods the route responds to.
     *
     * @var array
     */
    public $methods;

    /**
     * The route action array.
     *
     * @var array
     */
    public $action;

    /**
     * Indicates whether the route is a fallback route.
     *
     * @var bool
     */
    public $isFallback = false;

    /**
     * The controller instance.
     *
     * @var mixed
     */
    public $controller;

    /**
     * The default values for the route.
     *
     * @var array
     */
    public $defaults = [];

    /**
     * The regular expression requirements.
     *
     * @var array
     */
    public $wheres = [];

    /**
     * The array of matched parameters.
     *