     *
     * @return $this
     */
    public function setErrorBubbling(bool $errorBubbling): static;

    /**
     * Sets whether this field is required to be filled out when submitted.
     *
     * @return $this
     */
    public function setRequired(bool $required): static;

    /**
     * Sets the property path that the form should be mapped to.
     *
     * @param string|PropertyPathInterface|null $propertyPath The property path or null if the path should be set
     *                                                        automatically based on the form's name
     *
     * @return $this
     */
    public function setPropertyPath(string|PropertyPathInterface|null $propertyPath): static;

    /**
     * Sets whether the form should be mapped to an element of its
     * parent's data.
     *
     * @return $this
     */
    public function setMapped(bool $mapped): static;

    /**
     * Sets whether the form's data should be modified by reference.
     *
     * @return $this
     */
    public function setByReference(bool $byReference): static;

    /**
     * Sets whether the form should read and write the data of its parent.
     *
     * @return $this
     */
    public function setInheritData(bool $inheritData): static;

    /**
     * Sets whether the form should be compound.
     *
     * @return $this
     *
     * @see FormConfigInterface::getCompound()
     */
    public function setCompound(bool $compound): static;

    /**
     * Sets the resolved type.
     *
     * @return $this
     */
    public function setType(ResolvedFormTypeInterface $type): static;

    /**
     * Sets the initial data of the form.
     *
     * @param mixed $data The data of the form in model format
     *
     * @return $this
     */