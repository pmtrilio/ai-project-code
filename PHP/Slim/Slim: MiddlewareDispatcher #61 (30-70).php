
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