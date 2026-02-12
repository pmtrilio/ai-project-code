     * @param string|string[] $optionNames One or more option names
     *
     * @return $this
     *
     * @throws AccessException If called from a lazy option or normalizer
     */
    public function setRequired(string|array $optionNames): static
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be made required from a lazy option or normalizer.');
        }

        foreach ((array) $optionNames as $option) {
            $this->defined[$option] = true;
            $this->required[$option] = true;
        }

        return $this;
    }

    /**
     * Returns whether an option is required.
     *
     * An option is required if it was passed to {@link setRequired()}.
     */
    public function isRequired(string $option): bool
    {
        return isset($this->required[$option]);
    }

    /**
     * Returns the names of all required options.
     *
     * @return string[]
     *
     * @see isRequired()
     */
    public function getRequiredOptions(): array
    {
        return array_keys($this->required);
    }

    /**
     * Returns whether an option is missing a default value.
     *
     * An option is missing if it was passed to {@link setRequired()}, but not
     * to {@link setDefault()}. This option must be passed explicitly to
     * {@link resolve()}, otherwise an exception will be thrown.
     */
    public function isMissing(string $option): bool
    {
        return isset($this->required[$option]) && !\array_key_exists($option, $this->defaults);
    }

    /**
     * Returns the names of all options missing a default value.
     *
     * @return string[]
     */
    public function getMissingOptions(): array
    {
        return array_keys(array_diff_key($this->required, $this->defaults));
    }

    /**
     * Defines a valid option name.
     *
     * Defines an option name without setting a default value. The option will