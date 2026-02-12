        return $this;
    }

    public function isNested(string $option): bool
    {
        return isset($this->nested[$option]);
    }

    /**
     * Deprecates an option, allowed types or values.
     *
     * Instead of passing the message, you may also pass a closure with the
     * following signature:
     *
     *     function (Options $options, $value): string {
     *         // ...
     *     }
     *
     * The closure receives the value as argument and should return a string.
     * Return an empty string to ignore the option deprecation.
     *
     * The closure is invoked when {@link resolve()} is called. The parameter
     * passed to the closure is the value of the option after validating it
     * and before normalizing it.
     *
     * @param string          $package The name of the composer package that is triggering the deprecation
     * @param string          $version The version of the package that introduced the deprecation
     * @param string|\Closure $message The deprecation message to use
     *
     * @return $this
     */
    public function setDeprecated(string $option, string $package, string $version, string|\Closure $message = 'The option "%name%" is deprecated.'): static
    {
        if ($this->locked) {
            throw new AccessException('Options cannot be deprecated from a lazy option or normalizer.');
        }

        if (!isset($this->defined[$option])) {
            throw new UndefinedOptionsException(\sprintf('The option "%s" does not exist, defined options are: "%s".', $this->formatOptions([$option]), implode('", "', array_keys($this->defined))));
        }

        if (!\is_string($message) && !$message instanceof \Closure) {
            throw new InvalidArgumentException(\sprintf('Invalid type for deprecation message argument, expected string or \Closure, but got "%s".', get_debug_type($message)));
        }

        // ignore if empty string
        if ('' === $message) {
            return $this;
        }

        $this->deprecated[$option] = [
            'package' => $package,
            'version' => $version,
            'message' => $message,
        ];

        // Make sure the option is processed
        unset($this->resolved[$option]);

        return $this;
    }

    public function isDeprecated(string $option): bool
    {
        return isset($this->deprecated[$option]);
    }
