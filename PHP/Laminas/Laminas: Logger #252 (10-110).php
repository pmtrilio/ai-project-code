use Laminas\Log\Exception\InvalidArgumentException;
use Laminas\Log\Exception\RuntimeException;
use Laminas\Log\Processor\ProcessorInterface;
use Laminas\Log\Writer\WriterInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\SplPriorityQueue;
use Traversable;

use function array_reverse;
use function count;
use function error_get_last;
use function error_reporting;
use function get_class;
use function gettype;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function method_exists;
use function register_shutdown_function;
use function restore_error_handler;
use function restore_exception_handler;
use function set_error_handler;
use function set_exception_handler;
use function sprintf;
use function var_export;

use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;
use const E_CORE_ERROR;
use const E_CORE_WARNING;
use const E_DEPRECATED;
use const E_ERROR;
use const E_NOTICE;
use const E_PARSE;
use const E_RECOVERABLE_ERROR;
use const E_STRICT;
use const E_USER_DEPRECATED;
use const E_USER_ERROR;
use const E_USER_NOTICE;
use const E_USER_WARNING;
use const E_WARNING;

/**
 * Logging messages with a stack of backends
 */
class Logger implements LoggerInterface
{
    /**
     * @link http://tools.ietf.org/html/rfc3164
     *
     * @const int defined from the BSD Syslog message severities
     */
    public const EMERG  = 0;
    public const ALERT  = 1;
    public const CRIT   = 2;
    public const ERR    = 3;
    public const WARN   = 4;
    public const NOTICE = 5;
    public const INFO   = 6;
    public const DEBUG  = 7;

    /**
     * Map native PHP errors to priority
     *
     * @var array
     */
    public static $errorPriorityMap = [
        E_NOTICE            => self::NOTICE,
        E_USER_NOTICE       => self::NOTICE,
        E_WARNING           => self::WARN,
        E_CORE_WARNING      => self::WARN,
        E_USER_WARNING      => self::WARN,
        E_ERROR             => self::ERR,
        E_USER_ERROR        => self::ERR,
        E_CORE_ERROR        => self::ERR,
        E_RECOVERABLE_ERROR => self::ERR,
        E_PARSE             => self::ERR,
        E_COMPILE_ERROR     => self::ERR,
        E_COMPILE_WARNING   => self::ERR,
        E_STRICT            => self::DEBUG,
        E_DEPRECATED        => self::DEBUG,
        E_USER_DEPRECATED   => self::DEBUG,
    ];

    /**
     * Registered error handler
     *
     * @var bool
     */
    protected static $registeredErrorHandler = false;

    /**
     * Registered shutdown error handler
     *
     * @var bool
     */
    protected static $registeredFatalErrorShutdownFunction = false;