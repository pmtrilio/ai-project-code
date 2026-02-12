    protected Grammar $grammar;

    /**
     * The table the blueprint describes.
     *
     * @var string
     */
    protected $table;

    /**
     * The columns that should be added to the table.
     *
     * @var \Illuminate\Database\Schema\ColumnDefinition[]
     */
    protected $columns = [];

    /**
     * The commands that should be run for the table.
     *
     * @var \Illuminate\Support\Fluent[]
     */
    protected $commands = [];

    /**
     * The storage engine that should be used for the table.
     *
     * @var string
     */
    public $engine;

    /**
     * The default character set that should be used for the table.
     *
     * @var string
     */
    public $charset;

    /**
     * The collation that should be used for the table.
     *
     * @var string
     */
    public $collation;

    /**
     * Whether to make the table temporary.
     *
     * @var bool
     */
    public $temporary = false;

    /**
     * The column to add new columns after.
     *
     * @var string
     */
    public $after;

    /**
     * The blueprint state instance.
     *
     * @var \Illuminate\Database\Schema\BlueprintState|null
     */
    protected $state;

    /**
     * Create a new schema blueprint.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @param  string  $table
     * @param  \Closure|null  $callback
     */
    public function __construct(Connection $connection, $table, ?Closure $callback = null)
    {
        $this->connection = $connection;
        $this->grammar = $connection->getSchemaGrammar();
        $this->table = $table;

        if (! is_null($callback)) {
            $callback($this);
        }