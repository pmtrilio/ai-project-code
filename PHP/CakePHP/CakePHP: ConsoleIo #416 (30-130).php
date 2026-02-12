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
     * @var bool
     */
    protected bool $forceOverwrite = false;

    /**
     * @var bool
     */
    protected bool $interactive = true;

    /**
     * Constructor
     *
     * @param \Cake\Console\ConsoleOutput|null $out A ConsoleOutput object for stdout.
     * @param \Cake\Console\ConsoleOutput|null $err A ConsoleOutput object for stderr.
     * @param \Cake\Console\ConsoleInput|null $in A ConsoleInput object for stdin.
     * @param \Cake\Console\HelperRegistry|null $helpers A HelperRegistry instance
     */
    public function __construct(
        ?ConsoleOutput $out = null,
        ?ConsoleOutput $err = null,
        ?ConsoleInput $in = null,
        ?HelperRegistry $helpers = null,
    ) {
        $this->_out = $out ?: new ConsoleOutput('php://stdout');
        $this->_err = $err ?: new ConsoleOutput('php://stderr');
        $this->_in = $in ?: new ConsoleInput('php://stdin');
        $this->_helpers = $helpers ?: new HelperRegistry();
        $this->_helpers->setIo($this);
    }
