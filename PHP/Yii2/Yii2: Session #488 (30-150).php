 * $value2 = $session['name2'];  // get session variable 'name2'
 * foreach ($session as $name => $value) // traverse all session variables
 * $session['name3'] = $value3;  // set session variable 'name3'
 * ```
 *
 * Session can be extended to support customized session storage.
 * To do so, override [[useCustomStorage]] so that it returns true, and
 * override these methods with the actual logic about using custom storage:
 * [[openSession()]], [[closeSession()]], [[readSession()]], [[writeSession()]],
 * [[destroySession()]] and [[gcSession()]].
 *
 * Session also supports a special type of session data, called *flash messages*.
 * A flash message is available only in the current request and the next request.
 * After that, it will be deleted automatically. Flash messages are particularly
 * useful for displaying confirmation messages. To use flash messages, simply
 * call methods such as [[setFlash()]], [[getFlash()]].
 *
 * For more details and usage information on Session, see the [guide article on sessions](guide:runtime-sessions-cookies).
 *
 * @property-read array $allFlashes Flash messages (key => message or key => [message1, message2]).
 * @property string $cacheLimiter Current cache limiter.
 * @property array $cookieParams The session cookie parameters.
 * @property-read int $count The number of session variables.
 * @property-write string $flash The key identifying the flash message. Note that flash messages and normal
 * session variables share the same name space. If you have a normal session variable using the same name, its
 * value will be overwritten by this method.
 * @property float $gCProbability The probability (percentage) that the GC (garbage collection) process is
 * started on every session initialization.
 * @property bool $hasSessionId Whether the current request has sent the session ID.
 * @property string $id The current session ID.
 * @property-read bool $isActive Whether the session has started.
 * @property-read SessionIterator $iterator An iterator for traversing the session variables.
 * @property string $name The current session name.
 * @property string $savePath The current session save path, defaults to '/tmp'.
 * @property int $timeout The number of seconds after which data will be seen as 'garbage' and cleaned up. The
 * default value is 1440 seconds (or the value of "session.gc_maxlifetime" set in php.ini).
 * @property bool|null $useCookies The value indicating whether cookies should be used to store session IDs.
 * @property-read bool $useCustomStorage Whether to use custom storage.
 * @property bool $useStrictMode Whether strict mode is enabled or not.
 * @property bool $useTransparentSessionID Whether transparent sid support is enabled or not, defaults to
 * false.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 *
 * @implements \IteratorAggregate<array-key, mixed>
 * @implements \ArrayAccess<array-key, mixed>
 */
class Session extends Component implements \IteratorAggregate, \ArrayAccess, \Countable
{
    /**
     * @var string|null Holds the original session module (before a custom handler is registered) so that it can be
     * restored when a Session component without custom handler is used after one that has.
     */
    protected static $_originalSessionModule = null;
    /**
     * Polyfill for ini directive session.use-strict-mode for PHP < 5.5.2.
     */
    private static $_useStrictModePolyfill = false;
    /**
     * @var string the name of the session variable that stores the flash message data.
     */
    public $flashParam = '__flash';
    /**
     * @var \SessionHandlerInterface|array an object implementing the SessionHandlerInterface or a configuration array. If set, will be used to provide persistency instead of build-in methods.
     */
    public $handler;

    /**
     * @var string|null Holds the session id in case useStrictMode is enabled and the session id needs to be regenerated
     */
    protected $_forceRegenerateId = null;

    /**
     * @var array parameter-value pairs to override default session cookie parameters that are used for session_set_cookie_params() function
     * Array may have the following possible keys: 'lifetime', 'path', 'domain', 'secure', 'httponly'
     * @see https://www.php.net/manual/en/function.session-set-cookie-params.php
     */
    private $_cookieParams = ['httponly' => true];
    /**
     * @var array|null is used for saving session between recreations due to session parameters update.
     */
    private $_frozenSessionData;


    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init()
    {
        parent::init();
        register_shutdown_function([$this, 'close']);
        if ($this->getIsActive()) {
            Yii::warning('Session is already started', __METHOD__);
            $this->updateFlashCounters();
        }
    }

    /**
     * Returns a value indicating whether to use custom session storage.
     * This method should be overridden to return true by child classes that implement custom session storage.
     * To implement custom session storage, override these methods: [[openSession()]], [[closeSession()]],
     * [[readSession()]], [[writeSession()]], [[destroySession()]] and [[gcSession()]].
     * @return bool whether to use custom storage.
     */
    public function getUseCustomStorage()
    {
        return false;
    }

    /**
     * Starts the session.
     */
    public function open()
    {
        if ($this->getIsActive()) {
            return;
        }

        $this->registerSessionHandler();