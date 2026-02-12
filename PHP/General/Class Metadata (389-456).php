    }

    /**
     * Sets whether a group sequence provider should be used.
     *
     * @throws GroupDefinitionException
     */
    public function setGroupSequenceProvider(bool $active): void
    {
        if ($this->hasGroupSequence()) {
            throw new GroupDefinitionException('Defining a group sequence provider is not allowed with a static group sequence.');
        }

        if (null === $this->groupProvider && !$this->getReflectionClass()->implementsInterface(GroupSequenceProviderInterface::class)) {
            throw new GroupDefinitionException(\sprintf('Class "%s" must implement GroupSequenceProviderInterface.', $this->name));
        }

        $this->groupSequenceProvider = $active;
    }

    public function isGroupSequenceProvider(): bool
    {
        return $this->groupSequenceProvider;
    }

    public function setGroupProvider(?string $provider): void
    {
        $this->groupProvider = $provider;
    }

    public function getGroupProvider(): ?string
    {
        return $this->groupProvider;
    }

    public function getCascadingStrategy(): int
    {
        return $this->cascadingStrategy;
    }

    public function getTraversalStrategy(): int
    {
        return $this->traversalStrategy;
    }

    private function addPropertyMetadata(PropertyMetadataInterface $metadata): void
    {
        $property = $metadata->getPropertyName();

        $this->members[$property][] = $metadata;
    }

    private function checkConstraint(Constraint $constraint): void
    {
        if (!\in_array(Constraint::CLASS_CONSTRAINT, (array) $constraint->getTargets(), true)) {
            throw new ConstraintDefinitionException(\sprintf('The constraint "%s" cannot be put on classes.', get_debug_type($constraint)));
        }

        if ($constraint instanceof Composite) {
            foreach ($constraint->getNestedConstraints() as $nestedConstraint) {
                $this->checkConstraint($nestedConstraint);
            }
        }
    }

    private function canCascade(?\ReflectionType $type = null): bool
    {
        if (null === $type) {