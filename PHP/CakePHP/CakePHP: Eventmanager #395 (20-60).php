use Closure;
use InvalidArgumentException;
use ReflectionFunction;
use function Cake\Core\deprecationWarning;

/**
 * The event manager is responsible for keeping track of event listeners, passing the correct
 * data to them, and firing them in the correct order, when associated events are triggered. You
 * can create multiple instances of this object to manage local events or keep a single instance
 * and pass it around to manage all events in your app.
 */
class EventManager implements EventManagerInterface
{
    /**
     * The default priority queue value for new, attached listeners
     *
     * @var int
     */
    public static int $defaultPriority = 10;

    /**
     * The globally available instance, used for dispatching events attached from any scope
     *
     * @var \Cake\Event\EventManager|null
     */
    protected static ?EventManager $_generalManager = null;

    /**
     * List of listener callbacks associated to
     *
     * @var array
     */
    protected array $_listeners = [];

    /**
     * Internal flag to distinguish a common manager from the singleton
     *
     * @var bool
     */
    protected bool $_isGlobal = false;
