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