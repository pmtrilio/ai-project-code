use Slim\Factory\Psr17\SlimHttpPsr17Factory;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;
use Slim\Interfaces\Psr17FactoryProviderInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;

/** @api */
class AppFactory
{
    protected static ?Psr17FactoryProviderInterface $psr17FactoryProvider = null;

    protected static ?ResponseFactoryInterface $responseFactory = null;

    protected static ?StreamFactoryInterface $streamFactory = null;

    protected static ?ContainerInterface $container = null;

    protected static ?CallableResolverInterface $callableResolver = null;

    protected static ?RouteCollectorInterface $routeCollector = null;

    protected static ?RouteResolverInterface $routeResolver = null;

    protected static ?MiddlewareDispatcherInterface $middlewareDispatcher = null;

    protected static bool $slimHttpDecoratorsAutomaticDetectionEnabled = true;

    /**
     * @template TContainerInterface of (ContainerInterface|null)
     * @param TContainerInterface $container
     * @return (TContainerInterface is ContainerInterface ? App<TContainerInterface> : App<ContainerInterface|null>)
     */
    public static function create(
        ?ResponseFactoryInterface $responseFactory = null,
        ?ContainerInterface $container = null,
        ?CallableResolverInterface $callableResolver = null,
        ?RouteCollectorInterface $routeCollector = null,
        ?RouteResolverInterface $routeResolver = null,
        ?MiddlewareDispatcherInterface $middlewareDispatcher = null
    ): App {