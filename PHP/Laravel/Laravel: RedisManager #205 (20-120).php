     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The name of the default driver.
     *
     * @var string
     */
    protected $driver;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The Redis server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The Redis connections.
     *
     * @var mixed
     */
    protected $connections;

    /**
     * Indicates whether event dispatcher is set on connections.
     *
     * @var bool
     */
    protected $events = false;

    /**
     * Create a new Redis manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  string  $driver
     * @param  array  $config
     */
    public function __construct($app, $driver, array $config)
    {
        $this->app = $app;
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * Get a Redis connection by name.
     *
     * @param  string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection($name = null)
    {
        $name = $name ?: 'default';

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        return $this->connections[$name] = $this->configure(
            $this->resolve($name), $name
        );
    }

    /**
     * Resolve the given connection by name.
     *
     * @param  string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name = null)
    {
        $name = $name ?: 'default';

        $options = $this->config['options'] ?? [];

        if (isset($this->config[$name])) {
            return $this->connector()->connect(
                $this->parseConnectionConfiguration($this->config[$name]),
                array_merge(Arr::except($options, 'parameters'), ['parameters' => Arr::get($options, 'parameters.'.$name, Arr::get($options, 'parameters', []))])
            );
        }

        if (isset($this->config['clusters'][$name])) {
            return $this->resolveCluster($name);
        }

        throw new InvalidArgumentException("Redis connection [{$name}] not configured.");
    }