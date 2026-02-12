     * @return void
     */
    public function completed(JobPayload $payload, $failed = false, $silenced = false);

    /**
     * Delete the given monitored jobs by IDs.
     *
     * @param  array  $ids
     * @return void
     */
    public function deleteMonitored(array $ids);

    /**
     * Trim the recent job list.
     *
     * @return void
     */
    public function trimRecentJobs();

    /**
     * Trim the failed job list.
     *
     * @return void
     */
    public function trimFailedJobs();

    /**
     * Trim the monitored job list.
     *
     * @return void
     */
    public function trimMonitoredJobs();

    /**
     * Find a failed job by ID.
     *
     * @param  string  $id
     * @return \stdClass|null
     */
    public function findFailed($id);

    /**
     * Mark the job as failed.
     *
     * @param  \Exception  $exception
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Laravel\Horizon\JobPayload  $payload
     * @return void
     */
    public function failed($exception, $connection, $queue, JobPayload $payload);

    /**
     * Store the retry job ID on the original job record.
     *
     * @param  string  $id
     * @param  string  $retryId
     * @return void
     */
    public function storeRetryReference($id, $retryId);

    /**
     * Delete a failed job by ID.
     *
     * @param  string  $id
     * @return int
     */
    public function deleteFailed($id);