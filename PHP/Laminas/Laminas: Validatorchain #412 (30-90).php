 *     options?: array<string, mixed>,
 *     break_chain_on_failure?: bool,
 *     priority?: int,
 * }
 * @psalm-type ValidatorChainSpecification = array<array-key, ValidatorSpecification|ValidatorInterface>
 */
final class ValidatorChain implements Countable, IteratorAggregate, ValidatorChainInterface
{
    /**
     * Default priority at which validators are added
     *
     * @deprecated Use ValidatorChainInterface::DEFAULT_PRIORITY instead.
     */
    public const DEFAULT_PRIORITY = ValidatorChainInterface::DEFAULT_PRIORITY;

    /**
     * Validator chain
     *
     * @var PriorityQueue<QueueElement, int>
     */
    private PriorityQueue $validators;

    /**
     * Array of validation failure messages
     *
     * @var array<string, string>
     */
    private array $messages = [];

    /**
     * Initialize validator chain
     */
    public function __construct(
        private ValidatorPluginManager|null $pluginManager = null,
    ) {
        /** @var PriorityQueue<QueueElement, int> $queue */
        $queue            = new PriorityQueue();
        $this->validators = $queue;
    }

    /**
     * Return the count of attached validators
     */
    public function count(): int
    {
        return count($this->validators);
    }

    /**
     * Retrieve the Validator Plugin Manager used by this instance
     *
     * If you need an instance of the plugin manager, you should retrieve it from your DI container. This method is
     * only for internal use and is kept for compatibility with laminas-inputfilter.
     *
     * It is not subject to BC because it is marked as internal and the method may be removed in a minor release.
     *
     * @psalm-internal \Laminas
     * @psalm-internal \LaminasTest
     */
    public function getPluginManager(): ValidatorPluginManager
    {