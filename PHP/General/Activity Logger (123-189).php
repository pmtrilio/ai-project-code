
    public function useLog(?string $logName): static
    {
        $this->getActivity()->log_name = $logName;

        return $this;
    }

    public function inLog(?string $logName): static
    {
        return $this->useLog($logName);
    }

    public function tap(callable $callback, ?string $eventName = null): static
    {
        call_user_func($callback, $this->getActivity(), $eventName);

        return $this;
    }

    public function enableLogging(): static
    {
        $this->logStatus->enable();

        return $this;
    }

    public function disableLogging(): static
    {
        $this->logStatus->disable();

        return $this;
    }

    public function log(string $description): ?ActivityContract
    {
        if ($this->logStatus->disabled()) {
            return null;
        }

        $activity = $this->activity;

        $activity->description = $this->replacePlaceholders(
            $activity->description ?? $description,
            $activity
        );

        if (isset($activity->subject) && method_exists($activity->subject, 'tapActivity')) {
            $this->tap([$activity->subject, 'tapActivity'], $activity->event ?? '');
        }

        $activity->save();

        $this->activity = null;

        return $activity;
    }

    public function withoutLogs(Closure $callback): mixed
    {
        if ($this->logStatus->disabled()) {
            return $callback();
        }

        $this->logStatus->disable();

        try {