     * @return $this
     */
    public function setErrorBubbling(bool $errorBubbling): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormConfigBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.');
        }

        $this->errorBubbling = $errorBubbling;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRequired(bool $required): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormConfigBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.');
        }

        $this->required = $required;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPropertyPath(string|PropertyPathInterface|null $propertyPath): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormConfigBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.');
        }

        if (null !== $propertyPath && !$propertyPath instanceof PropertyPathInterface) {
            $propertyPath = new PropertyPath($propertyPath);
        }

        $this->propertyPath = $propertyPath;

        return $this;
    }

    /**
     * @return $this
     */
    public function setMapped(bool $mapped): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormConfigBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.');
        }

        $this->mapped = $mapped;

        return $this;
    }

    /**
     * @return $this
     */
    public function setByReference(bool $byReference): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormConfigBuilder methods cannot be accessed anymore once the builder is turned into a FormConfigInterface instance.');
        }
