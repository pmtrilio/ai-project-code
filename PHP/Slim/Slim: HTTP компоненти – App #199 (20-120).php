use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteResolver;
use Slim\Routing\RouteRunner;

use function strtoupper;

/**
 * @api
 * @template TContainerInterface of (ContainerInterface|null)
 * @template-extends RouteCollectorProxy<TContainerInterface>
 */
class App extends RouteCollectorProxy implements RequestHandlerInterface
{
    /**
     * Current version
     *
     * @var string
     */
    public const VERSION = '4.15.1';

    protected RouteResolverInterface $routeResolver;

    protected MiddlewareDispatcherInterface $middlewareDispatcher;

    /**
     * @param TContainerInterface $container
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ) {
        parent::__construct(
            $responseFactory,
            $callableResolver ?? new CallableResolver($container),
            $container,
            $routeCollector
        );

        $this->routeResolver = $routeResolver ?? new RouteResolver($this->routeCollector);
        $routeRunner = new RouteRunner($this->routeResolver, $this->routeCollector->getRouteParser(), $this);

        if (!$middlewareDispatcher) {
            $middlewareDispatcher = new MiddlewareDispatcher($routeRunner, $this->callableResolver, $container);
        } else {
            $middlewareDispatcher->seedMiddlewareStack($routeRunner);
        }

        $this->middlewareDispatcher = $middlewareDispatcher;
    }

    /**
     * @return RouteResolverInterface
     */
    public function getRouteResolver(): RouteResolverInterface
    {
        return $this->routeResolver;
    }

    /**
     * @return MiddlewareDispatcherInterface
     */
    public function getMiddlewareDispatcher(): MiddlewareDispatcherInterface
    {
        return $this->middlewareDispatcher;
    }

    /**
     * @param MiddlewareInterface|string|callable $middleware
     * @return App<TContainerInterface>
     */
    public function add($middleware): self
    {
        $this->middlewareDispatcher->add($middleware);
        return $this;
    }

    /**
     * @param MiddlewareInterface $middleware
     * @return App<TContainerInterface>
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewareDispatcher->addMiddleware($middleware);
        return $this;
    }

    /**
     * Add the Slim built-in routing middleware to the app middleware stack
     *