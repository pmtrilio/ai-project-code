 *         callback: FilterInterface|(callable(mixed): mixed),
 *         priority?: int,
 *     }>
 * }
 * @implements IteratorAggregate<array-key, InstanceType>
 * @implements FilterChainInterface<mixed>
 */
final class FilterChain implements FilterChainInterface, Countable, IteratorAggregate
{
    /** @var PriorityQueue<InstanceType, int> */
    private PriorityQueue $filters;

    /**
     * @param FilterChainConfiguration $options
     * @throws ContainerExceptionInterface If any filter cannot be retrieved from the plugin manager.
     */
    public function __construct(
        private readonly FilterPluginManager $plugins,
        array $options = [],
    ) {
        /** @var PriorityQueue<InstanceType, int> $priorityQueue */
        $priorityQueue = new PriorityQueue();
        $this->filters = $priorityQueue;

        $callbacks = $options['callbacks'] ?? [];
        foreach ($callbacks as $spec) {
            $this->attach(
                $spec['callback'],
                $spec['priority'] ?? self::DEFAULT_PRIORITY,
            );
        }

        $filters = $options['filters'] ?? [];
        foreach ($filters as $spec) {
            if (is_callable($spec) || $spec instanceof FilterInterface) {
                $this->attach($spec);
                continue;
            }

            $this->attachByName(
                $spec['name'],
                $spec['options'] ?? [],
                $spec['priority'] ?? self::DEFAULT_PRIORITY,
            );
        }
    }

    /** Return the count of attached filters */
    public function count(): int
    {
        return count($this->filters);
    }

    public function attach(FilterInterface|callable $callback, int $priority = self::DEFAULT_PRIORITY): self
    {
        $this->filters->insert($callback, $priority);

        return $this;
    }

    public function attachByName(string $name, array $options = [], int $priority = self::DEFAULT_PRIORITY): self