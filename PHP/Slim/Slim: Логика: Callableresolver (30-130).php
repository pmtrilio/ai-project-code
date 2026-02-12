 * @template TContainerInterface of (ContainerInterface|null)
 */
final class CallableResolver implements AdvancedCallableResolverInterface
{
    public static string $callablePattern = '!^([^\:]+)\:([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$!';

    /** @var TContainerInterface $container */
    private ?ContainerInterface $container;

    /**
     * @param TContainerInterface $container
     */
    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($toResolve): callable
    {
        $toResolve = $this->prepareToResolve($toResolve);
        if (is_callable($toResolve)) {
            return $this->bindToContainer($toResolve);
        }
        $resolved = $toResolve;
        if (is_string($toResolve)) {
            $resolved = $this->resolveSlimNotation($toResolve);
            $resolved[1] ??= '__invoke';
        }
        $callable = $this->assertCallable($resolved, $toResolve);
        return $this->bindToContainer($callable);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRoute($toResolve): callable
    {
        return $this->resolveByPredicate($toResolve, [$this, 'isRoute'], 'handle');
    }

    /**
     * {@inheritdoc}
     */
    public function resolveMiddleware($toResolve): callable
    {
        return $this->resolveByPredicate($toResolve, [$this, 'isMiddleware'], 'process');
    }

    /**
     * @param callable|array{class-string, string}|string $toResolve
     *
     * @throws RuntimeException
     */
    private function resolveByPredicate($toResolve, callable $predicate, string $defaultMethod): callable
    {
        $toResolve = $this->prepareToResolve($toResolve);
        if (is_callable($toResolve)) {
            return $this->bindToContainer($toResolve);
        }
        $resolved = $toResolve;
        if ($predicate($toResolve)) {
            $resolved = [$toResolve, $defaultMethod];
        }
        if (is_string($toResolve)) {
            [$instance, $method] = $this->resolveSlimNotation($toResolve);
            if ($method === null && $predicate($instance)) {
                $method = $defaultMethod;
            }
            $resolved = [$instance, $method ?? '__invoke'];
        }
        $callable = $this->assertCallable($resolved, $toResolve);
        return $this->bindToContainer($callable);
    }

    /**
     * @param mixed $toResolve
     */
    private function isRoute($toResolve): bool
    {
        return $toResolve instanceof RequestHandlerInterface;
    }

    /**
     * @param mixed $toResolve
     */
    private function isMiddleware($toResolve): bool
    {
        return $toResolve instanceof MiddlewareInterface;
    }

    /**
     * @throws RuntimeException
     *
     * @return array{object, string|null} [Instance, Method Name]
     */
    private function resolveSlimNotation(string $toResolve): array
    {
        /** @psalm-suppress ArgumentTypeCoercion */