
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
     * @return \Illuminate\Process\FakeProcessResult
     */
    public function result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
    {
        return new FakeProcessResult(
            output: $output,
            errorOutput: $errorOutput,
            exitCode: $exitCode,
        );
    }

    /**
     * Begin describing a fake process lifecycle.
     *
     * @return \Illuminate\Process\FakeProcessDescription
     */
    public function describe()
    {
        return new FakeProcessDescription;
    }

    /**
     * Begin describing a fake process sequence.
     *
     * @param  array  $processes
     * @return \Illuminate\Process\FakeProcessSequence
     */
    public function sequence(array $processes = [])
    {
        return new FakeProcessSequence($processes);
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param  \Closure|array|null  $callback
     * @return $this
     */
    public function fake(Closure|array|null $callback = null)
    {
        $this->recording = true;

        if (is_null($callback)) {
            $this->fakeHandlers = ['*' => fn () => new FakeProcessResult];

            return $this;
        }

        if ($callback instanceof Closure) {
            $this->fakeHandlers = ['*' => $callback];

            return $this;
        }

        foreach ($callback as $command => $handler) {
            $this->fakeHandlers[is_numeric($command) ? '*' : $command] = $handler instanceof Closure
                ? $handler
                : fn () => $handler;
        }

        return $this;
    }

    /**
     * Determine if the process factory has fake process handlers and is recording processes.
     *
     * @return bool
     */
    public function isRecording()
    {
        return $this->recording;
    }

    /**
     * Record the given process if processes should be recorded.
     *
     * @param  \Illuminate\Process\PendingProcess  $process
     * @param  \Illuminate\Contracts\Process\ProcessResult  $result
     * @return $this
     */