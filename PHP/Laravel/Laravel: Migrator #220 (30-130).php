     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The migration repository implementation.
     *
     * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface
     */
    protected $repository;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The custom connection resolver callback.
     *
     * @var \Closure|null
     */
    protected static $connectionResolverCallback;

    /**
     * The name of the default connection.
     *
     * @var string
     */
    protected $connection;

    /**
     * The paths to all of the migration files.
     *
     * @var string[]
     */
    protected $paths = [];

    /**
     * The paths that have already been required.
     *
     * @var array<string, \Illuminate\Database\Migrations\Migration|null>
     */
    protected static $requiredPathCache = [];

    /**
     * The output interface implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * The pending migrations to skip.
     *
     * @var list<string>
     */
    protected static $withoutMigrations = [];

    /**
     * Create a new migrator instance.
     *
     * @param  \Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
     * @param  \Illuminate\Database\ConnectionResolverInterface  $resolver
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $dispatcher
     */
    public function __construct(
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files,
        ?Dispatcher $dispatcher = null,
    ) {
        $this->files = $files;
        $this->events = $dispatcher;
        $this->resolver = $resolver;
        $this->repository = $repository;
    }

    /**
     * Run the pending migrations at a given path.
     *
     * @param  string[]|string  $paths
     * @param  array<string, mixed>  $options
     * @return string[]
     */
    public function run($paths = [], array $options = [])
    {
        // Once we grab all of the migration files for the path, we will compare them
        // against the migrations that have already been run for this package then
        // run each of the outstanding migrations against a database connection.
        $files = $this->getMigrationFiles($paths);
