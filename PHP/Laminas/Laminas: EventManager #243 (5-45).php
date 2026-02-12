use ArrayObject;

use function array_keys;
use function array_merge;
use function array_unique;
use function get_debug_type;
use function is_callable;
use function is_string;
use function krsort;
use function sprintf;

/**
 * Event manager: notification system
 *
 * Use the EventManager when you want to create a per-instance notification
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