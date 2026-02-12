     *
     *    // See PHP stream options for valid SSL configuration settings.
     *    'ssl' => ['verify_peer' => false],
     *
     *    // How quickly to retry a connection after we time out or it  closes.
     *    // Note that this setting is overridden by 'backoff' strategies.
     *    'retryInterval'  => 100,
     *
     *     // Which backoff algorithm to use.  'decorrelated jitter' is
     *     // likely the best one for most solution, but there are many
     *     // to choose from:
     *     //     REDIS_BACKOFF_ALGORITHM_DEFAULT
     *     //     REDIS_BACKOFF_ALGORITHM_CONSTANT
     *     //     REDIS_BACKOFF_ALGORITHM_UNIFORM
     *     //     REDIS_BACKOFF_ALGORITHM_EXPONENTIAL
     *     //     REDIS_BACKOFF_ALGORITHM_FULL_JITTER
     *     //     REDIS_BACKOFF_ALGORITHM_EQUAL_JITTER
     *     //     REDIS_BACKOFF_ALGORITHM_DECORRELATED_JITTER
     *     // 'base', and 'cap' are in milliseconds and represent the first
     *     // delay redis will use when reconnecting, and the maximum delay
     *     // we will reach while retrying.
     *    'backoff' => [
     *        'algorithm' => Redis::BACKOFF_ALGORITHM_DECORRELATED_JITTER,
     *        'base'      => 500,
     *        'cap'       => 750,
     *    ]
     *];
     *```
     *
     * Note: If you do wish to connect via the constructor, only 'host' is
     *       strictly required, which will cause PhpRedis to connect to that
     *       host on Redis' default port (6379).
     *
     *
     * @see Redis::connect()
     * @see https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/
     * @param array|null $options
     *
     * @return Redis
     *
     * @example
     * $redis = new Redis(['host' => '127.0.0.1', 'port' => 6380]);
     *
     */
    public function __construct(?array $options = null);

    /**
     * Destructor to clean up the Redis object.
     *
     * This method will disconnect from Redis. If the connection is persistento
     * it will be stashed for future reuse.
     *
     */
    public function __destruct();

    /**
     * Compress a value with the currently configured compressor (Redis::OPT_COMPRESSION)
     * exactly the same way PhpRedis does before sending data to Redis.
     *
     * @see Redis::setOption()
     *
     * @param  string $value The value to be compressed
     * @return string        The compressed result (or the original value if compression is disabled)
     *
     * @example
     * $redis->_compress('payload');
     *
     */