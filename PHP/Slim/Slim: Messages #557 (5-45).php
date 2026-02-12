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