use LogicException;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;
use Symfony\Component\Process\Process;

class PendingProcess
{
    use Conditionable;

    /**
     * The process factory instance.
     *
     * @var \Illuminate\Process\Factory
     */
    protected $factory;

    /**
     * The command to invoke the process.
     *
     * @var array<array-key, string>|string|null
     */
    public $command;

    /**
     * The working directory of the process.
     *
     * @var string|null
     */
    public $path;

    /**
     * The maximum number of seconds the process may run.
     *
     * @var int|null
     */
    public $timeout = 60;

    /**
     * The maximum number of seconds the process may go without returning output.
     *
     * @var int
     */
    public $idleTimeout;

    /**
     * The additional environment variables for the process.
     *
     * @var array
     */
    public $environment = [];

    /**
     * The standard input data that should be piped into the command.
     *
     * @var string|int|float|bool|resource|\Traversable|null
     */
    public $input;

    /**
     * Indicates whether output should be disabled for the process.
     *
     * @var bool
     */
    public $quietly = false;

    /**
     * Indicates if TTY mode should be enabled.
     *
     * @var bool
     */
    public $tty = false;

    /**
     * The options that will be passed to "proc_open".
     *
     * @var array
     */
    public $options = [];

    /**
     * The registered fake handler callbacks.
     *
     * @var array
     */
    protected $fakeHandlers = [];

    /**
     * Create a new pending process instance.
     *
     * @param  \Illuminate\Process\Factory  $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Specify the command that will invoke the process.
     *
     * @param  array<array-key, string>|string  $command
     * @return $this
     */
    public function command(array|string $command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Specify the working directory of the process.
     *
     * @param  string  $path
     * @return $this
     */
    public function path(string $path)
    {
        $this->path = $path;

        return $this;
    }