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