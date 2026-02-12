use Cake\Log\Engine\ConsoleLog;
use Cake\Log\Log;
use RuntimeException;
use SplFileObject;

/**
 * A wrapper around the various IO operations shell tasks need to do.
 *
 * Packages up the stdout, stderr, and stdin streams providing a simple
 * consistent interface for shells to use. This class also makes mocking streams
 * easy to do in unit tests.
 */
class ConsoleIo
{
    /**
     * Output constant making verbose shells.
     *
     * @var int
     */
    public const VERBOSE = 2;

    /**
     * Output constant for making normal shells.
     *
     * @var int
     */
    public const NORMAL = 1;

    /**
     * Output constants for making quiet shells.
     *
     * @var int
     */
    public const QUIET = 0;

    /**
     * The output stream
     *
     * @var \Cake\Console\ConsoleOutput
     */
    protected ConsoleOutput $_out;

    /**
     * The error stream
     *
     * @var \Cake\Console\ConsoleOutput
     */
    protected ConsoleOutput $_err;

    /**
     * The input stream
     *
     * @var \Cake\Console\ConsoleInput
     */
    protected ConsoleInput $_in;

    /**
     * The helper registry.
     *
     * @var \Cake\Console\HelperRegistry
     */
    protected HelperRegistry $_helpers;

    /**
     * The current output level.
     *
     * @var int
     */
    protected int $_level = self::NORMAL;

    /**
     * The number of bytes last written to the output stream
     * used when overwriting the previous message.
     *
     * @var int
     */
    protected int $_lastWritten = 0;

    /**
     * Whether files should be overwritten
     *