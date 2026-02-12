     * Determine if the daemon should process on this iteration.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  string  $connectionName
     * @param  string  $queue
     * @return bool
     */
    protected function daemonShouldRun(WorkerOptions $options, $connectionName, $queue)
    {
        return ! ((($this->isDownForMaintenance)() && ! $options->force) ||
            $this->paused ||
            $this->events->until(new Looping($connectionName, $queue)) === false);
    }

    /**
     * Pause the worker for the current loop.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @return array|null
     */
    protected function pauseWorker(WorkerOptions $options, $lastRestart)
    {
        $this->sleep($options->sleep > 0 ? $options->sleep : 1);

        return $this->stopIfNecessary($options, $lastRestart);
    }

    /**
     * Determine the exit code to stop the process if necessary.
     *
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @param  int  $lastRestart
     * @param  int  $startTime
     * @param  int  $jobsProcessed
     * @param  mixed  $job
     * @return array|null
     */
    protected function stopIfNecessary(WorkerOptions $options, $lastRestart, $startTime = 0, $jobsProcessed = 0, $job = null)
    {
        return match (true) {
            $this->shouldQuit => [static::EXIT_SUCCESS, WorkerStopReason::Interrupted],
            $this->memoryExceeded($options->memory) => [static::$memoryExceededExitCode ?? static::EXIT_MEMORY_LIMIT, WorkerStopReason::MaxMemoryExceeded],
            $this->queueShouldRestart($lastRestart) => [static::EXIT_SUCCESS, WorkerStopReason::ReceivedRestartSignal],
            $options->stopWhenEmpty && is_null($job) => [static::EXIT_SUCCESS, WorkerStopReason::QueueEmpty],
            $options->maxTime && hrtime(true) / 1e9 - $startTime >= $options->maxTime => [static::EXIT_SUCCESS, WorkerStopReason::MaxTimeExceeded],
            $options->maxJobs && $jobsProcessed >= $options->maxJobs => [static::EXIT_SUCCESS, WorkerStopReason::MaxJobsExceeded],
            default => null
        };
    }

    /**
     * Process the next job on the queue.
     *
     * @param  string  $connectionName
     * @param  string  $queue
     * @param  \Illuminate\Queue\WorkerOptions  $options
     * @return void
     */
    public function runNextJob($connectionName, $queue, WorkerOptions $options)
    {
        $job = $this->getNextJob(
            $this->manager->connection($connectionName), $queue
        );

        // If we're able to pull a job off of the stack, we will process it and then return
        // from this method. If there is no job on the queue, we will "sleep" the worker
        // for the specified number of seconds, then keep processing jobs after sleep.