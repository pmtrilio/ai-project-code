 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Slim\Interfaces\AdvancedCallableResolverInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\MiddlewareDispatcherInterface;

use function class_exists;
use function function_exists;
use function is_callable;
use function is_string;
use function preg_match;
use function sprintf;

/**
 * @api
 * @template TContainerInterface of (ContainerInterface|null)
 */
class MiddlewareDispatcher implements MiddlewareDispatcherInterface
{
    /**
     * Tip of the middleware call stack
     */
    protected RequestHandlerInterface $tip;

    protected ?CallableResolverInterface $callableResolver;

    /** @var TContainerInterface $container */
    protected ?ContainerInterface $container;

    /**
     * @param TContainerInterface $container
     */
    public function __construct(
        RequestHandlerInterface $kernel,
        ?CallableResolverInterface $callableResolver = null,
        ?ContainerInterface $container = null
    ) {
        $this->seedMiddlewareStack($kernel);
        $this->callableResolver = $callableResolver;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function seedMiddlewareStack(RequestHandlerInterface $kernel): void
    {
        $this->tip = $kernel;
    }

    /**
     * Invoke the middleware stack
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->tip->handle($request);
    }

    /**
     * Add a new middleware to the stack
     *
     * Middleware are organized as a stack. That means middleware
     * that have been added before will be executed after the newly
     * added one (last in, first out).
     *
     * @param MiddlewareInterface|string|callable $middleware
     */
    public function add($middleware): MiddlewareDispatcherInterface