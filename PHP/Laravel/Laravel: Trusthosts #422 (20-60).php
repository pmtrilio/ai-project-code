     * @var array<int, string>|(callable(): array<int, string>)|null
     */
    protected static $alwaysTrust;

    /**
     * Indicates whether subdomains of the application URL should be trusted.
     *
     * @var bool|null
     */
    protected static $subdomains;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    public function hosts()
    {
        if (is_null(static::$alwaysTrust)) {
            return [$this->allSubdomainsOfApplicationUrl()];
        }

        $hosts = match (true) {
            is_array(static::$alwaysTrust) => static::$alwaysTrust,
            is_callable(static::$alwaysTrust) => call_user_func(static::$alwaysTrust),
            default => [],
        };

        if (static::$subdomains) {
            $hosts[] = $this->allSubdomainsOfApplicationUrl();
        }