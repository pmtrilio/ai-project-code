     * Link passed formula.
     */
    public function link(string $formula, bool $force = false): string
    {
        return $this->cli->runAsUser(
            sprintf('brew link %s%s', $formula, $force ? ' --force' : ''),
            function ($exitCode, $errorOutput) use ($formula) {
                output($errorOutput);

                throw new DomainException('Brew was unable to link ['.$formula.'].');
            }
        );
    }

    /**
     * Unlink passed formula.
     */
    public function unlink(string $formula): string
    {
        return $this->cli->runAsUser(
            sprintf('brew unlink %s', $formula),
            function ($exitCode, $errorOutput) use ($formula) {
                output($errorOutput);

                throw new DomainException('Brew was unable to unlink ['.$formula.'].');
            }
        );
    }

    /**
     * Get all the currently running brew services.
     */
    public function getAllRunningServices(): Collection
    {
        return $this->getRunningServicesAsRoot()
            ->concat($this->getRunningServicesAsUser())
            ->unique();
    }

    /**
     * Get the currently running brew services as root.
     * i.e. /Library/LaunchDaemons (started at boot).
     */
    public function getRunningServicesAsRoot(): Collection
    {
        return $this->getRunningServices();
    }

    /**
     * Get the currently running brew services.
     * i.e. ~/Library/LaunchAgents (started at login).
     */
    public function getRunningServicesAsUser(): Collection
    {
        return $this->getRunningServices(true);
    }

    /**
     * Get the currently running brew services.
     */
    public function getRunningServices(bool $asUser = false): Collection
    {
        $command = 'brew services list | grep started | awk \'{ print $1; }\'';
        $onError = function ($exitCode, $errorOutput) {
            output($errorOutput);

            throw new DomainException('Brew was unable to check which services are running.');
        };