     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param mixed[] $context
     */
    public function alert(string|\Stringable $message, array $context = []): void;

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param mixed[] $context
     */
    public function critical(string|\Stringable $message, array $context = []): void;

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param mixed[] $context
     */
    public function error(string|\Stringable $message, array $context = []): void;

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param mixed[] $context
     */
    public function warning(string|\Stringable $message, array $context = []): void;

    /**
     * Normal but significant events.
     *
     * @param mixed[] $context
     */