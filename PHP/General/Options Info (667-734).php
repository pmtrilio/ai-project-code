        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Adds allowed types for an option.
     *
     * The types are merged with the allowed types defined previously.
     *
     * Any type for which a corresponding is_<type>() function exists is
     * acceptable. Additionally, fully-qualified class or interface names may
     * be passed.
     *
     * @param string|string[] $allowedTypes One or more accepted types
     *
     * @return $this
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function addAllowedTypes(string $option, string|array $allowedTypes): static
    {
        if ($this->locked) {
            throw new AccessException('Allowed types cannot be added from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(\sprintf('The option "%s" does not exist. Defined options are: "%s".', $this->formatOptions([$option]), implode('", "', array_keys($this->defined))));
        }

        if (!isset($this->allowedTypes[$option])) {
            $this->allowedTypes[$option] = (array) $allowedTypes;
        } else {
            $this->allowedTypes[$option] = array_merge($this->allowedTypes[$option], (array) $allowedTypes);
        }

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    /**
     * Defines an option configurator with the given name.
     */
    public function define(string $option): OptionConfigurator
    {
        if (isset($this->defined[$option])) {
            throw new OptionDefinitionException(\sprintf('The option "%s" is already defined.', $option));
        }

        return new OptionConfigurator($option, $this);
    }

    /**
     * Sets an info message for an option.
     *
     * @return $this
     *
     * @throws UndefinedOptionsException If the option is undefined
     * @throws AccessException           If called from a lazy option or normalizer
     */
    public function setInfo(string $option, string $info): static
    {
        if ($this->locked) {
            throw new AccessException('The Info message cannot be set from a lazy option or normalizer.');