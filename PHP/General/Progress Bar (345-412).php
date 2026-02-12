     *
     * @param int|null $max     Number of steps to complete the bar (0 if indeterminate), null to leave unchanged
     * @param int      $startAt The starting point of the bar (useful e.g. when resuming a previously started bar)
     */
    public function start(?int $max = null, int $startAt = 0): void
    {
        $this->startTime = time();
        $this->step = $startAt;
        $this->startingStep = $startAt;

        $startAt > 0 ? $this->setProgress($startAt) : $this->percent = 0.0;

        if (null !== $max) {
            $this->setMaxSteps($max);
        }

        $this->display();
    }

    /**
     * Advances the progress output X steps.
     *
     * @param int $step Number of steps to advance
     */
    public function advance(int $step = 1): void
    {
        $this->setProgress($this->step + $step);
    }

    /**
     * Sets whether to overwrite the progressbar, false for new line.
     */
    public function setOverwrite(bool $overwrite): void
    {
        $this->overwrite = $overwrite;
    }

    public function setProgress(int $step): void
    {
        if ($this->max && $step > $this->max) {
            $this->max = $step;
        } elseif ($step < 0) {
            $step = 0;
        }

        $redrawFreq = $this->redrawFreq ?? (($this->max ?? 10) / 10);
        $prevPeriod = $redrawFreq ? (int) ($this->step / $redrawFreq) : 0;
        $currPeriod = $redrawFreq ? (int) ($step / $redrawFreq) : 0;
        $this->step = $step;
        $this->percent = match ($this->max) {
            null => 0,
            0 => 1,
            default => (float) $this->step / $this->max,
        };
        $timeInterval = microtime(true) - $this->lastWriteTime;

        // Draw regardless of other limits
        if ($this->max === $step) {
            $this->display();

            return;
        }

        // Throttling
        if ($timeInterval < $this->minSecondsBetweenRedraws) {
            return;
        }
