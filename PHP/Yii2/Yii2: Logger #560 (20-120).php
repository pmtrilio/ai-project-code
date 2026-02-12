 * - [[Yii::trace()]]
 * - [[Yii::error()]]
 * - [[Yii::warning()]]
 * - [[Yii::info()]]
 * - [[Yii::beginProfile()]]
 * - [[Yii::endProfile()]]
 *
 * For more details and usage information on Logger, see the [guide article on logging](guide:runtime-logging).
 *
 * When the application ends or [[flushInterval]] is reached, Logger will call [[flush()]]
 * to send logged messages to different log targets, such as [[FileTarget|file]], [[EmailTarget|email]],
 * or [[DbTarget|database]], with the help of the [[dispatcher]].
 *
 * @property-read array $dbProfiling The first element indicates the number of SQL statements executed, and
 * the second element the total time spent in SQL execution.
 * @property-read float $elapsedTime The total elapsed time in seconds for current request.
 * @property-read array $profiling The profiling results. Each element is an array consisting of these
 * elements: `info`, `category`, `timestamp`, `trace`, `level`, `duration`, `memory`, `memoryDiff`. The `memory`
 * and `memoryDiff` values are available since version 2.0.11.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Logger extends Component
{
    /**
     * Error message level. An error message is one that indicates the abnormal termination of the
     * application and may require developer's handling.
     */
    public const LEVEL_ERROR = 0x01;
    /**
     * Warning message level. A warning message is one that indicates some abnormal happens but
     * the application is able to continue to run. Developers should pay attention to this message.
     */
    public const LEVEL_WARNING = 0x02;
    /**
     * Informational message level. An informational message is one that includes certain information
     * for developers to review.
     */
    public const LEVEL_INFO = 0x04;
    /**
     * Tracing message level. A tracing message is one that reveals the code execution flow.
     */
    public const LEVEL_TRACE = 0x08;
    /**
     * Profiling message level. This indicates the message is for profiling purpose.
     */
    public const LEVEL_PROFILE = 0x40;
    /**
     * Profiling message level. This indicates the message is for profiling purpose. It marks the beginning
     * of a profiling block.
     */
    public const LEVEL_PROFILE_BEGIN = 0x50;
    /**
     * Profiling message level. This indicates the message is for profiling purpose. It marks the end
     * of a profiling block.
     */
    public const LEVEL_PROFILE_END = 0x60;
    /**
     * @var array logged messages. This property is managed by [[log()]] and [[flush()]].
     * Each log message is of the following structure:
     *
     * ```
     * [
     *   [0] => message (mixed, can be a string or some complex data, such as an exception object)
     *   [1] => level (integer)
     *   [2] => category (string)
     *   [3] => timestamp (float, obtained by microtime(true))
     *   [4] => traces (array, debug backtrace, contains the application code call stacks)
     *   [5] => memory usage in bytes (int, obtained by memory_get_usage()), available since version 2.0.11.
     * ]
     * ```
     */
    public $messages = [];
    /**
     * @var int how many messages should be logged before they are flushed from memory and sent to targets.
     * Defaults to 1000, meaning the [[flush()]] method will be invoked once every 1000 messages logged.
     * Set this property to be 0 if you don't want to flush messages until the application terminates.
     * This property mainly affects how much memory will be taken by the logged messages.
     * A smaller value means less memory, but will increase the execution time due to the overhead of [[flush()]].
     */
    public $flushInterval = 1000;
    /**
     * @var int how much call stack information (file name and line number) should be logged for each message.
     * If it is greater than 0, at most that number of call stacks will be logged. Note that only application
     * call stacks are counted.
     */
    public $traceLevel = 0;
    /**
     * @var Dispatcher the message dispatcher.
     */
    public $dispatcher;
    /**
     * @var array of event names used to get statistical results of DB queries.
     * @since 2.0.41
     */
    public $dbEventNames = ['yii\db\Command::query', 'yii\db\Command::execute'];
    /**
     * @var bool whether the profiling-aware mode should be switched on.
     * If on, [[flush()]] makes sure that profiling blocks are flushed in pairs. In case that any dangling messages are
     * detected these are kept for the next flush interval to find their pair. To prevent memory leaks, when number of