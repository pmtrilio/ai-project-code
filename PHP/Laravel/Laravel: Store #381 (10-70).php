use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Uri;
use Illuminate\Support\ViewErrorBag;
use RuntimeException;
use SessionHandlerInterface;
use stdClass;

class Store implements Session
{
    use Macroable;

    /**
     * The session ID.
     *
     * @var string
     */
    protected $id;

    /**
     * The session name.
     *
     * @var string
     */
    protected $name;

    /**
     * The session attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The session handler implementation.
     *
     * @var \SessionHandlerInterface
     */
    protected $handler;

    /**
     * The session store's serialization strategy.
     *
     * @var string
     */
    protected $serialization = 'php';

    /**
     * Session store started status.
     *
     * @var bool
     */
    protected $started = false;

    /**
     * Create a new session instance.
     *
     * @param  string  $name
     * @param  \SessionHandlerInterface  $handler
     * @param  string|null  $id