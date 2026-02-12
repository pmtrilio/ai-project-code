     */
    public function processes()
    {
        return $this->processPools->map->processes()->collapse();
    }

    /**
     * Get the processes that are still terminating.
     *
     * @return \Illuminate\Support\Collection
     */
    public function terminatingProcesses()
    {
        return $this->processPools->map->terminatingProcesses()->collapse();
    }

    /**
     * Get the total active process count, including processes pending termination.
     *
     * @return int
     */
    public function totalProcessCount()
    {
        return $this->processPools->sum->totalProcessCount();
    }

    /**
     * Get the total active process count by asking the OS.
     *
     * @return int
     */
    public function totalSystemProcessCount()
    {
        return app(SystemProcessCounter::class)->get($this->name);
    }

    /**
     * Get the process ID for this supervisor.
     *
     * @return int
     */
    public function pid()
    {
        return getmypid();
    }

    /**
     * Get the current memory usage (in megabytes).
     *
     * @return float
     */
    public function memoryUsage()
    {
        return memory_get_usage() / 1024 / 1024;
    }

    /**
     * Determine if the supervisor is paused.
     *
     * @return bool
     */
    public function isPaused()
    {
        return ! $this->working;
    }

    /**
     * Set the output handler.