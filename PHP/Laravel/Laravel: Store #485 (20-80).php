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
     * @param  string  $serialization
     */
    public function __construct($name, SessionHandlerInterface $handler, $id = null, $serialization = 'php')
    {
        $this->setId($id);
        $this->name = $name;
        $this->handler = $handler;
        $this->serialization = $serialization;
    }
