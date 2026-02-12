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