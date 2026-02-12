use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Throwable;
use Stringable;
use WeakMap;

/**
 * Monolog log channel
 *
 * It contains a stack of Handlers and a stack of Processors,
 * and uses them to store records that are added to it.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @final
 */
class Logger implements LoggerInterface, ResettableInterface
{
    /**
     * Detailed debug information
     *
     * @deprecated Use \Monolog\Level::Debug
     */
    public const DEBUG = 100;

    /**
     * Interesting events
     *
     * Examples: User logs in, SQL logs.
     *
     * @deprecated Use \Monolog\Level::Info
     */
    public const INFO = 200;

    /**
     * Uncommon events
     *
     * @deprecated Use \Monolog\Level::Notice
     */
    public const NOTICE = 250;

    /**
     * Exceptional occurrences that are not errors
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     *
     * @deprecated Use \Monolog\Level::Warning
     */
    public const WARNING = 300;

    /**
     * Runtime errors
     *
     * @deprecated Use \Monolog\Level::Error
     */
    public const ERROR = 400;

    /**
     * Critical conditions
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @deprecated Use \Monolog\Level::Critical
     */
    public const CRITICAL = 500;

    /**
     * Action must be taken immediately
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     *
     * @deprecated Use \Monolog\Level::Alert
     */
    public const ALERT = 550;

    /**
     * Urgent alert.
     *
     * @deprecated Use \Monolog\Level::Emergency
     */
    public const EMERGENCY = 600;

    /**
     * Monolog API version
     *
     * This is only bumped when API breaks are done and should
     * follow the major version of the library
     */
    public const API = 3;

    /**
     * Mapping between levels numbers defined in RFC 5424 and Monolog ones
     *
     * @phpstan-var array<int, Level> $rfc_5424_levels
     */
    private const RFC_5424_LEVELS = [
        7 => Level::Debug,
        6 => Level::Info,
        5 => Level::Notice,
        4 => Level::Warning,
        3 => Level::Error,
        2 => Level::Critical,
        1 => Level::Alert,
        0 => Level::Emergency,
    ];

    protected string $name;

    /**
     * The handler stack
     *
     * @var list<HandlerInterface>
     */
    protected array $handlers;

    /**
     * Processors that will process all log records
     *
     * To process records of a single handler instead, add the processor on that specific handler
     *