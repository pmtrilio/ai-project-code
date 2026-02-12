    public function close(): void
    {
        foreach ($this->handlers as $handler) {
            $handler->close();
        }
    }

    /**
     * Ends a log cycle and resets all handlers and processors to their initial state.
     *
     * Resetting a Handler or a Processor means flushing/cleaning all buffers, resetting internal
     * state, and getting it back to a state in which it can receive log records again.
     *
     * This is useful in case you want to avoid logs leaking between two requests or jobs when you
     * have a long running process like a worker or an application server serving multiple requests
     * in one process.
     */
    public function reset(): void
    {
        foreach ($this->handlers as $handler) {
            if ($handler instanceof ResettableInterface) {
                $handler->reset();
            }
        }

        foreach ($this->processors as $processor) {
            if ($processor instanceof ResettableInterface) {
                $processor->reset();
            }
        }
    }

    /**
     * Gets the name of the logging level as a string.
     *
     * This still returns a string instead of a Level for BC, but new code should not rely on this method.
     *
     * @throws \Psr\Log\InvalidArgumentException If level is not defined
     *
     * @phpstan-param  value-of<Level::VALUES>|Level $level
     * @phpstan-return value-of<Level::NAMES>
     *
     * @deprecated Since 3.0, use {@see toMonologLevel} or {@see \Monolog\Level->getName()} instead
     */
    public static function getLevelName(int|Level $level): string
    {
        return self::toMonologLevel($level)->getName();
    }

    /**
     * Converts PSR-3 levels to Monolog ones if necessary
     *
     * @param  int|string|Level|LogLevel::*      $level Level number (monolog) or name (PSR-3)
     * @throws \Psr\Log\InvalidArgumentException If level is not defined
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public static function toMonologLevel(string|int|Level $level): Level
    {
        if ($level instanceof Level) {
            return $level;
        }

        if (\is_string($level)) {
            if (is_numeric($level)) {
                $levelEnum = Level::tryFrom((int) $level);
                if ($levelEnum === null) {