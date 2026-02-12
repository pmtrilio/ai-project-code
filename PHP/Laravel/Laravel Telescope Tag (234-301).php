
    /**
     * Determine if the incoming entry is a cache entry.
     *
     * @return bool
     */
    public function isCache()
    {
        return $this->type === EntryType::CACHE;
    }

    /**
     * Determine if the incoming entry is an authorization gate check.
     *
     * @return bool
     */
    public function isGate()
    {
        return $this->type === EntryType::GATE;
    }

    /**
     * Determine if the incoming entry is a failed job.
     *
     * @return bool
     */
    public function isFailedJob()
    {
        return $this->type === EntryType::JOB &&
               ($this->content['status'] ?? null) === 'failed';
    }

    /**
     * Determine if the incoming entry is a reportable exception.
     *
     * @return bool
     */
    public function isReportableException()
    {
        return false;
    }

    /**
     * Determine if the incoming entry is an exception.
     *
     * @return bool
     */
    public function isException()
    {
        return false;
    }

    /**
     * Determine if the incoming entry is a dump.
     *
     * @return bool
     */
    public function isDump()
    {
        return false;
    }

    /**
     * Determine if the incoming entry is a log entry.
     *
     * @return bool
     */
    public function isLog()