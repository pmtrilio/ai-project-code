 * @author Nicolas Grekas <p@tchwork.com>
 */
class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];
    private array $sorted = [];
    private array $optimized;

    public function __construct()
    {
        if (__CLASS__ === static::class) {
            $this->optimized = [];
        }
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $eventName ??= $event::class;

        if (isset($this->optimized)) {
            $listeners = $this->optimized[$eventName] ?? (empty($this->listeners[$eventName]) ? [] : $this->optimizeListeners($eventName));
        } else {
            $listeners = $this->getListeners($eventName);
        }

        if ($listeners) {
            $this->callListeners($listeners, $eventName, $event);
        }

        return $event;
    }

    public function getListeners(?string $eventName = null): array
    {
        if (null !== $eventName) {
            if (empty($this->listeners[$eventName])) {
                return [];
            }

            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);