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