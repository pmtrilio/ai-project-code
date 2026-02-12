     *
     * @param array $parameters The parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Gets a parameter value.
     */
    public function getParameter(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    /**
     * Checks if a parameter value is set for the given parameter.
     */
    public function hasParameter(string $name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }

    /**
     * Sets a parameter value.
     *
     * @return $this
     */
    public function setParameter(string $name, mixed $parameter): static
    {
        $this->parameters[$name] = $parameter;

        return $this;
    }

    public function isSecure(): bool
    {
        return 'https' === $this->scheme;
    }
}
