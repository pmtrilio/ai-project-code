
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
