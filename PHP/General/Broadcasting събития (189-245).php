
            return $event instanceof ShouldRescue
                ? $this->rescue($dispatch)
                : $dispatch();
        }

        $queue = null;

        if (method_exists($event, 'broadcastQueue')) {
            $queue = $event->broadcastQueue();
        } elseif (isset($event->broadcastQueue)) {
            $queue = $event->broadcastQueue;
        } elseif (isset($event->queue)) {
            $queue = $event->queue;
        }

        if (is_null($queue)) {
            $queue = $this->resolveQueueFromQueueRoute($event) ?? null;
        }

        $broadcastEvent = new BroadcastEvent(clone $event);

        if ($event instanceof ShouldBeUnique) {
            $broadcastEvent = new UniqueBroadcastEvent(clone $event);

            if ($this->mustBeUniqueAndCannotAcquireLock($broadcastEvent)) {
                return;
            }
        }

        $push = fn () => $this->app->make('queue')
            ->connection(
                $event->connection
                    ?? $this->resolveConnectionFromQueueRoute($event)
                    ?? null
            )
            ->pushOn($queue, $broadcastEvent);

        $event instanceof ShouldRescue
            ? $this->rescue($push)
            : $push();
    }

    /**
     * Determine if the broadcastable event must be unique and determine if we can acquire the necessary lock.
     *
     * @param  mixed  $event
     * @return bool
     */
    protected function mustBeUniqueAndCannotAcquireLock($event)
    {
        return ! (new UniqueLock(
            method_exists($event, 'uniqueVia')
                ? $event->uniqueVia()
                : $this->app->make(Cache::class)
        ))->acquire($event);
    }