     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * The callback that should be used to generate UUIDs.
     *
     * @var callable|null
     */
    protected static $uuidFactory;

    /**
     * The callback that should be used to generate ULIDs.
     *
     * @var callable|null
     */
    protected static $ulidFactory;

    /**
     * The callback that should be used to generate random strings.
     *
     * @var callable|null
     */
    protected static $randomStringFactory;

    /**
     * Get a new stringable object from the given string.