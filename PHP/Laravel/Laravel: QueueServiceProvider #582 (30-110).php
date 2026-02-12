    use SerializesAndRestoresModelIdentifiers;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->configureSerializableClosureUses();

        $this->registerManager();
        $this->registerConnection();
        $this->registerWorker();
        $this->registerListener();
        $this->registerRoutes();
        $this->registerFailedJobServices();
    }

    /**
     * Configure serializable closures uses.
     *
     * @return void
     */
    protected function configureSerializableClosureUses()
    {
        SerializableClosure::transformUseVariablesUsing(function ($data) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->getSerializedPropertyValue($value);
            }

            return $data;
        });

        SerializableClosure::resolveUseVariablesUsing(function ($data) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->getRestoredPropertyValue($value);
            }

            return $data;
        });
    }

    /**
     * Register the queue manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('queue', function ($app) {
            // Once we have an instance of the queue manager, we will register the various
            // resolvers for the queue connectors. These connectors are responsible for
            // creating the classes that accept queue configs and instantiate queues.
            return tap(new QueueManager($app), function ($manager) {
                $this->registerConnectors($manager);
            });
        });
    }

    /**
     * Register the default queue connection binding.
     *
     * @return void
     */
    protected function registerConnection()
    {
        $this->app->singleton('queue.connection', function ($app) {
            return $app['queue']->connection();
        });
    }

    /**
     * Register the connectors on the queue manager.
     *
     * @param  \Illuminate\Queue\QueueManager  $manager
     * @return void
     */
    public function registerConnectors($manager)
    {
        foreach (['Null', 'Sync', 'Deferred', 'Background', 'Failover', 'Database', 'Redis', 'Beanstalkd', 'Sqs'] as $connector) {