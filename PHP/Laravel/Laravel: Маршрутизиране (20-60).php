use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Routing\Events\Routing;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use ReflectionClass;
use stdClass;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @mixin \Illuminate\Routing\RouteRegistrar
 */
class Router implements BindingRegistrar, RegistrarContract
{
    use Macroable {
        __call as macroCall;
    }
    use Tappable;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The route collection instance.