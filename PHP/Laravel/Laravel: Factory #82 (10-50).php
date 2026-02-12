
class Factory
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * Indicates if the process factory has faked process handlers.
     *
     * @var bool
     */
    protected $recording = false;

    /**
     * All of the recorded processes.
     *
     * @var array
     */
    protected $recorded = [];

    /**
     * The registered fake handler callbacks.
     *
     * @var array
     */
    protected $fakeHandlers = [];

    /**
     * Indicates that an exception should be thrown if any process is not faked.
     *
     * @var bool
     */
    protected $preventStrayProcesses = false;

    /**
     * Create a new fake process response for testing purposes.
     *
     * @param  array|string  $output
     * @param  array|string  $errorOutput
     * @param  int  $exitCode