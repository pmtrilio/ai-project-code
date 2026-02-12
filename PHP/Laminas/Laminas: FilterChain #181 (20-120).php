/**
 * @psalm-type InstanceType = FilterInterface|(callable(mixed): mixed)
 * @psalm-type FilterSpecification = array{
 *     name: string|class-string<FilterInterface>,
 *     options?: array<string, mixed>,
 *     priority?: int,
 * }|InstanceType
 * @psalm-type FilterChainConfiguration = array{
 *     filters?: list<FilterSpecification>,
 *     callbacks?: list<array{
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
    {
        if ($options === []) {
            /** @psalm-var FilterInterface $filter */
            $filter = $this->plugins->get($name);
        } else {
            /** @psalm-var FilterInterface $filter */
            $filter = $this->plugins->build($name, $options);
        }

        return $this->attach($filter, $priority);
    }

    /**
     * Merge the filter chain with the one given in parameter
     *
     * @return $this
     */
    public function merge(FilterChain $filterChain): self
    {
        foreach ($filterChain->filters->toArray(PriorityQueue::EXTR_BOTH) as $item) {
            $this->attach($item['data'], $item['priority']);
        }

        return $this;
    }

    public function filter(mixed $value): mixed
    {
        foreach ($this as $filter) {
            /** @var mixed $value */