<?php

declare(strict_types=1);

namespace Laminas\Validator;

use Countable;
use IteratorAggregate;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\PriorityQueue;
use Laminas\Validator\Exception\InvalidSpecificationArrayException;
use Traversable;

use function array_replace;
use function assert;
use function count;
use function is_array;
use function is_bool;
use function is_int;
use function is_string;
use function rsort;

use const SORT_NUMERIC;

/**
 * @psalm-type QueueElement = array{instance: ValidatorInterface, breakChainOnFailure: bool}
 * @implements IteratorAggregate<array-key, QueueElement>
 * @psalm-type ValidatorSpecification = array{
 *     name: non-empty-string|class-string<ValidatorInterface>,
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