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