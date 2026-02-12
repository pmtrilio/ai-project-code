
namespace Slim;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
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