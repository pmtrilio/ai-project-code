use RuntimeException;
use InvalidArgumentException;

/**
 * Flash messages
 */
class Messages
{
    /**
     * Messages from previous request
     *
     * @var string[]
     */
    protected $fromPrevious = [];

    /**
     * Messages for current request
     *
     * @var string[]
     */
    protected $forNow = [];

    /**
     * Messages for next request
     *
     * @var string[]
     */
    protected $forNext = [];

    /**
     * Message storage
     *
     * @var null|array|ArrayAccess
     */
    protected $storage;

    /**
     * Message storage key
     *
     * @var string
     */
    protected $storageKey = 'slimFlash';

    /**
     * Create new Flash messages service provider
     *
     * @param null|array|ArrayAccess $storage
     * @throws RuntimeException if the session cannot be found
     * @throws InvalidArgumentException if the store is not array-like
     */
    public function __construct(&$storage = null, $storageKey = null)
    {
        if (is_string($storageKey) && $storageKey) {
            $this->storageKey = $storageKey;
        }

        // Set storage
        if (is_array($storage) || $storage instanceof ArrayAccess) {
            $this->storage = &$storage;
        } elseif (is_null($storage)) {
            if (!isset($_SESSION)) {
                throw new RuntimeException('Flash messages middleware failed. Session not found.');
            }
            $this->storage = &$_SESSION;
        } else {
            throw new InvalidArgumentException('Flash messages storage must be an array or implement \ArrayAccess');
        }

        // Load messages from previous request
        if (isset($this->storage[$this->storageKey]) && is_array($this->storage[$this->storageKey])) {
            $this->fromPrevious = $this->storage[$this->storageKey];
        }
        $this->storage[$this->storageKey] = [];
    }

    /**
     * Add flash message for the next request
     *
     * @param string $key The key to store the message under
     * @param mixed  $message Message to show on next request
     */
    public function addMessage($key, $message)
    {
        // Create Array for this key
        if (!isset($this->storage[$this->storageKey][$key])) {
            $this->storage[$this->storageKey][$key] = [];
        }

        // Push onto the array
        $this->storage[$this->storageKey][$key][] = $message;
    }

    /**
     * Add flash message for current request
     *
     * @param string $key The key to store the message under
     * @param mixed  $message Message to show for the current request
     */
    public function addMessageNow($key, $message)
    {
        // Create Array for this key