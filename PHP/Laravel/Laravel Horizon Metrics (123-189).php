     * Attempt to acquire a lock to monitor the queue wait times.
     *
     * @return bool
     */
    public function acquireWaitTimeMonitorLock();

    /**
     * Clear the metrics for a key.
     *
     * @param  string  $key
     * @return void
     */
    public function forget($key);

    /**
     * Delete all stored metrics information.
     *
     * @return void
     */
    public function clear();
}
