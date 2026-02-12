    }

    /**
     * Gets whether to catch exceptions or not during commands execution.
     */
    public function areExceptionsCaught(): bool
    {
        return $this->catchExceptions;
    }

    /**
     * Sets whether to catch exceptions or not during commands execution.
     */
    public function setCatchExceptions(bool $boolean): void
    {
        $this->catchExceptions = $boolean;
    }

    /**
     * Sets whether to catch errors or not during commands execution.
     */
    public function setCatchErrors(bool $catchErrors = true): void
    {
        $this->catchErrors = $catchErrors;
    }

    /**
     * Gets whether to automatically exit after a command execution or not.
     */
    public function isAutoExitEnabled(): bool
    {
        return $this->autoExit;
    }

    /**
     * Sets whether to automatically exit after a command execution or not.
     */
    public function setAutoExit(bool $boolean): void
    {
        $this->autoExit = $boolean;
    }

    /**
     * Gets the name of the application.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the application name.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the application version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Sets the application version.