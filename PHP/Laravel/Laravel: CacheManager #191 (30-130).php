     *
     * @var array
     */
    protected $stores = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new Cache manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a cache store instance by name, wrapped in a repository.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function store($name = null)
    {
        $name = $name ?? $this->getDefaultDriver();

        return $this->stores[$name] ??= $this->resolve($name);
    }

    /**
     * Get a cache driver instance.
     *
     * @param  string|null  $driver
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function driver($driver = null)
    {
        return $this->store($driver);
    }

    /**
     * Get a memoized cache driver instance.
     *
     * @param  string|null  $driver
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function memo($driver = null)
    {
        $driver = $driver ?? $this->getDefaultDriver();

        $bindingKey = "cache.__memoized:{$driver}";

        $isSpy = isset($this->app['cache']) && $this->app['cache'] instanceof LegacyMockInterface;

        $this->app->scopedIf($bindingKey, function () use ($driver, $isSpy) {
            $repository = $this->repository(
                new MemoizedStore($driver, $this->store($driver)), ['events' => false]
            );

            return $isSpy ? Mockery::spy($repository) : $repository;
        });

        return $this->app->make($bindingKey);
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \Illuminate\Contracts\Cache\Repository
     *
     * @throws \InvalidArgumentException
     */
    public function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Cache store [{$name}] is not defined.");
        }

        $config = Arr::add($config, 'store', $name);

        return $this->build($config);
    }

    /**
     * Build a cache repository with the given configuration.
     *
     * @param  array  $config
     * @return \Illuminate\Cache\Repository
     */
    public function build(array $config)
    {
        $config = Arr::add($config, 'store', $config['name'] ?? 'ondemand');