 * system for your objects.
 *
 * @final This class should not be extended
 */
class EventManager implements EventManagerInterface
{
    /**
     * Subscribed events and their listeners
     *
     * STRUCTURE:
     * [
     *     <string name> => [
     *         <int priority> => [
     *             0 => [<callable listener>, ...]
     *         ],
     *         ...
     *     ],
     *     ...
     * ]
     *
     * NOTE:
     * This structure helps us to reuse the list of listeners
     * instead of first iterating over it and generating a new one
     * -> In result it improves performance by up to 25% even if it looks a bit strange
     *
     * @var array<string, array<int, array{0: list<callable>}>>
     */
    protected $events = [];

    /** @var EventInterface Prototype to use when creating an event at trigger(). */
    protected $eventPrototype;

    /**
     * Identifiers, used to pull shared signals from SharedEventManagerInterface instance
     *
     * @var array
     */
    protected $identifiers = [];

    /**
     * Shared event manager
     *
     * @var null|SharedEventManagerInterface
     */
    protected $sharedManager;

    /**
     * Constructor
     *
     * Allows optionally specifying identifier(s) to use to pull signals from a
     * SharedEventManagerInterface.
     */
    public function __construct(?SharedEventManagerInterface $sharedEventManager = null, array $identifiers = [])
    {
        if ($sharedEventManager) {
            $this->sharedManager = $sharedEventManager;
            $this->setIdentifiers($identifiers);
        }

        $this->eventPrototype = new Event();
    }

    /**
     * @inheritDoc
     */
    public function setEventPrototype(EventInterface $prototype)
    {
        $this->eventPrototype = $prototype;
    }

    /**
     * Retrieve the shared event manager, if composed.
     *
     * @return null|SharedEventManagerInterface $sharedEventManager
     */
    public function getSharedManager()
    {
        return $this->sharedManager;
    }

    /**
     * @inheritDoc
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * @inheritDoc
     */
    public function setIdentifiers(array $identifiers)
    {
        $this->identifiers = array_unique($identifiers);
    }

    /**
     * @inheritDoc
     */
    public function addIdentifiers(array $identifiers)
    {