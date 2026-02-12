 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface EventDispatcherInterface extends ContractsEventDispatcherInterface
{
    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param int $priority The higher this value, the earlier an event
     *                      listener will be triggered in the chain (defaults to 0)
     *
     * @return void
     */
    public function addListener(string $eventName, callable $listener, int $priority = 0);

    /**
     * Adds an event subscriber.
     *
     * The subscriber is asked for all the events it is
     * interested in and added as a listener for these events.
     *
     * @return void
     */
    public function addSubscriber(EventSubscriberInterface $subscriber);

    /**
     * Removes an event listener from the specified events.
     *
     * @return void
     */
    public function removeListener(string $eventName, callable $listener);

    /**
     * @return void
     */
    public function removeSubscriber(EventSubscriberInterface $subscriber);

    /**
     * Gets the listeners of a specific event or all listeners sorted by descending priority.
     *
     * @return array<callable[]|callable>