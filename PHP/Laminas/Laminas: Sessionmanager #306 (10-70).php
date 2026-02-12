use function array_key_exists;
use function array_merge;
use function assert;
use function constant;
use function defined;
use function headers_sent;
use function is_array;
use function is_string;
use function iterator_to_array;
use function preg_match;
use function register_shutdown_function;
use function session_destroy;
use function session_id;
use function session_name;
use function session_regenerate_id;
use function session_set_save_handler;
use function session_start;
use function session_status;
use function session_write_close;
use function setcookie;

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
