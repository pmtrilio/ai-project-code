use function array_replace;
use function array_reverse;
use function class_implements;
use function in_array;
use function is_array;

/**
 * @api
 * @template TContainerInterface of (ContainerInterface|null)
 */
class Route implements RouteInterface, RequestHandlerInterface
{
    /**
     * HTTP methods supported by this route
     *
     * @var string[]
     */
    protected array $methods = [];

    /**
     * Route identifier
     */
    protected string $identifier;

    /**
     * Route name
     */
    protected ?string $name = null;

    /**
     * Parent route groups
     *
     * @var RouteGroupInterface[]
     */
    protected array $groups;

    protected InvocationStrategyInterface $invocationStrategy;

    /**
     * Route parameters
     *
     * @var array<string, string>
     */
    protected array $arguments = [];

    /**
     * Route arguments parameters
     *
     * @var array<string, string>
     */
    protected array $savedArguments = [];

    /**
     * Container
     * @var TContainerInterface $container
     */
    protected ?ContainerInterface $container = null;

    /** @var MiddlewareDispatcher<TContainerInterface> $middlewareDispatcher */
    protected MiddlewareDispatcher $middlewareDispatcher;
