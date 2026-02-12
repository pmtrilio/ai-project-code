 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use RuntimeException;
use Slim\App;
use Slim\Factory\Psr17\Psr17Factory;
use Slim\Factory\Psr17\Psr17FactoryProvider;
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
        static::$responseFactory = $responseFactory ?? static::$responseFactory;
        return new App(
            self::determineResponseFactory(),
            $container ?? static::$container,
            $callableResolver ?? static::$callableResolver,