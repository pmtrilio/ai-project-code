
use const PHP_SESSION_ACTIVE;

/**
 * Session ManagerInterface implementation utilizing ext/session
 *
 * @final
 */
class SessionManager extends AbstractManager
{
    /**
     * Default options when a call to {@link destroy()} is made
     * - send_expire_cookie: whether or not to send a cookie expiring the current session cookie
     * - clear_storage: whether or not to empty the storage object of any stored values
     *
     * @deprecated This property will be removed in version 3.0
     *
     * @var array
     */
    protected $defaultDestroyOptions = [
        'send_expire_cookie' => true,
        'clear_storage'      => false,
    ];

    /**
     * @deprecated This property will be removed in version 3.0
     *
     * @var array Default session manager options
     */
    protected $defaultOptions = [
        'attach_default_validators' => true,
    ];

    /** @var array Default validators */
    protected $defaultValidators = [
        Validator\Id::class,
    ];

    /** @var string value returned by session_name() */
    protected $name;

    /** @var EventManagerInterface Validation chain to determine if session is valid */
    protected $validatorChain;

    /**
     * Constructor
     *
     * @throws Exception\RuntimeException
     */
    public function __construct(
        ?Config\ConfigInterface $config = null,
        ?Storage\StorageInterface $storage = null,
        ?SaveHandler\SaveHandlerInterface $saveHandler = null,
        array $validators = [],
        array $options = []
    ) {
        $options = array_merge($this->defaultOptions, $options);
        if ($options['attach_default_validators']) {
            $validators = array_merge($this->defaultValidators, $validators);
        }

        parent::__construct($config, $storage, $saveHandler, $validators);
        register_shutdown_function([$this, 'writeClose']);
    }

    /**
     * Does a session exist and is it currently active?
     *
     * @return bool
     */
    public function sessionExists()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        /**
         * @var string|false $sid
         */
        $sid = defined('SID') ? constant('SID') : false;

        if ($sid !== false && $this->getId()) {
            return true;
        }

        if (headers_sent()) {
            return true;
        }

        return false;
    }

    /**
     * Start session
     *
     * if No session currently exists, attempt to start it. Calls
     * {@link isValid()} once session_start() is called, and raises an
     * exception if validation fails.
     *
     * @param bool $preserveStorage        If set to true, current session storage will not be overwritten by the
     *                                     contents of $_SESSION.