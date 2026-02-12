                    'completed_at' => str_replace(',', '.', microtime(true)),
                ]
            );

            $pipe->expireat(
                $payload->id(), CarbonImmutable::now()->addMinutes($this->monitoredJobExpires)->getTimestamp()
            );
        });
    }

    /**
     * Mark the given jobs as released / pending.
     *
     * @param  string  $connection
     * @param  string  $queue
     * @param  \Illuminate\Support\Collection  $payloads
     * @return void
     */
    public function migrated($connection, $queue, Collection $payloads)
    {
        $this->connection()->pipeline(function ($pipe) use ($payloads) {
            foreach ($payloads as $payload) {
                $pipe->hmset(
                    $payload->id(), [
                        'status' => 'pending',
                        'payload' => $payload->value,
                        'updated_at' => str_replace(',', '.', microtime(true)),
                    ]
                );
            }
        });
    }

    /**
     * Handle the storage of a completed job.
     *
     * @param  \Laravel\Horizon\JobPayload  $payload
     * @param  bool  $failed
     * @param  bool  $silenced
     * @return void
     */
    public function completed(JobPayload $payload, $failed = false, $silenced = false)
    {
        if ($payload->isRetry()) {
            $this->updateRetryInformationOnParent($payload, $failed);
        }

        $this->connection()->pipeline(function ($pipe) use ($payload, $silenced) {
            $this->storeJobReference($pipe, $silenced ? 'silenced_jobs' : 'completed_jobs', $payload);
            $this->removeJobReference($pipe, 'pending_jobs', $payload);

            $pipe->hmset(
                $payload->id(), [
                    'status' => 'completed',
                    'completed_at' => str_replace(',', '.', microtime(true)),
                ]
            );

            $pipe->expireat($payload->id(), CarbonImmutable::now()->addMinutes($this->completedJobExpires)->getTimestamp());
        });
    }

    /**
     * Update the retry status of a job's parent.
     *
     * @param  \Laravel\Horizon\JobPayload  $payload
     * @param  bool  $failed