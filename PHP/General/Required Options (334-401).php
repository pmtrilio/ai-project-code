     * Defines an option name without setting a default value. The option will
     * be accepted when passed to {@link resolve()}. When not passed, the
     * option will not be included in the resolved options.
     *
     * @param string|string[] $optionNames One or more option names
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function setDefined(string|array $optionNames): static
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be defined from a lazy option or normalizer.');
        }

        foreach ((array) $optionNames as $option) {
            $this->defined[$option] = true;
        }

        return $this;
    }

    /**
     * Returns whether an option is defined.
     *
     * Returns true for any option passed to {@link setDefault()},
     * {@link setRequired()} or {@link setDefined()}.
     */
    public function isDefined(string $option): bool
    {
        return isset($this->defined[$option]);
    }

    /**
     * Returns the names of all defined options.
     *
     * @return string[]
     *
     * @see isDefined()
     */
    public function getDefinedOptions(): array
    {
        return array_keys($this->defined);
    }

    /**
     * Defines nested options.
     *
     * @param \Closure(self $resolver, Options $parent): void $nested
     *
     * @return $this
     */
    public function setOptions(string $option, \Closure $nested): static
    {
        if ($this->locked) {
            throw new AccessException('Nested options cannot be defined from a lazy option or normalizer.');
        }

        // Store closure for later evaluation
        $this->nested[$option][] = $nested;
        $this->defaults[$option] = [];
        $this->defined[$option] = true;

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;