    protected $grammar;

    /**
     * The Blueprint resolver callback.
     *
     * @var \Closure(\Illuminate\Database\Connection, string, \Closure|null): \Illuminate\Database\Schema\Blueprint
     */
    protected $resolver;

    /**
     * The default string length for migrations.
     *
     * @var int|null
     */
    public static $defaultStringLength = 255;

    /**
     * The default time precision for migrations.
     */
    public static ?int $defaultTimePrecision = 0;

    /**
     * The default relationship morph key type.
     *
     * @var string
     */
    public static $defaultMorphKeyType = 'int';

    /**
     * Create a new database Schema manager.
     *
     * @param  \Illuminate\Database\Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->grammar = $connection->getSchemaGrammar();
    }

    /**
     * Set the default string length for migrations.
     *
     * @param  int  $length
     * @return void
     */
    public static function defaultStringLength($length)
    {
        static::$defaultStringLength = $length;
    }

    /**
     * Set the default time precision for migrations.
     */
    public static function defaultTimePrecision(?int $precision): void
    {
        static::$defaultTimePrecision = $precision;
    }

    /**
     * Set the default morph key type for migrations.
     *
     * @param  string  $type
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public static function defaultMorphKeyType(string $type)
    {
        if (! in_array($type, ['int', 'uuid', 'ulid'])) {
            throw new InvalidArgumentException("Morph key type must be 'int', 'uuid', or 'ulid'.");
        }

        static::$defaultMorphKeyType = $type;
    }

    /**
     * Set the default morph key type for migrations to UUIDs.
     *
     * @return void
     */
    public static function morphUsingUuids()