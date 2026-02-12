     * @param  array<string, string>  $scopes
     */
    public static function tokensCan(array $scopes): void
    {
        static::$scopes = $scopes;
    }

    /**
     * Get or set when access tokens expire.
     */
    public static function tokensExpireIn(DateTimeInterface|DateInterval|null $date = null): DateInterval
    {
        if (is_null($date)) {
            return static::$tokensExpireIn ??= new DateInterval('P1Y');
        }

        return static::$tokensExpireIn = $date instanceof DateTimeInterface
            ? Date::now()->diff($date)
            : $date;
    }

    /**
     * Get or set when refresh tokens expire.
     */
    public static function refreshTokensExpireIn(DateTimeInterface|DateInterval|null $date = null): DateInterval
    {
        if (is_null($date)) {
            return static::$refreshTokensExpireIn ??= new DateInterval('P1Y');
        }

        return static::$refreshTokensExpireIn = $date instanceof DateTimeInterface
            ? Date::now()->diff($date)
            : $date;
    }

    /**
     * Get or set when personal access tokens expire.
     */
    public static function personalAccessTokensExpireIn(DateTimeInterface|DateInterval|null $date = null): DateInterval
    {
        if (is_null($date)) {
            return static::$personalAccessTokensExpireIn ??= new DateInterval('P1Y');
        }

        return static::$personalAccessTokensExpireIn = $date instanceof DateTimeInterface
            ? Date::now()->diff($date)
            : $date;
    }

    /**
     * Get or set the name for API token cookies.
     */
    public static function cookie(?string $cookie = null): string
    {
        if (is_null($cookie)) {
            return static::$cookie;
        }

        return static::$cookie = $cookie;
    }

    /**
     * Indicate that Passport should ignore incoming CSRF tokens.
     */
    public static function ignoreCsrfToken(bool $ignoreCsrfToken = true): void
    {
        static::$ignoreCsrfToken = $ignoreCsrfToken;
    }