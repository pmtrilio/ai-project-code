    /**
     * Sets the value of a property in the given object.
     *
     * @throws NoSuchPropertyException if the property does not exist or is not public
     */
    private function writeProperty(array $zval, string $property, mixed $value, bool $recursive = false): void
    {
        if (!\is_object($zval[self::VALUE])) {
            throw new NoSuchPropertyException(\sprintf('Cannot write property "%s" to an array. Maybe you should write the property path as "[%1$s]" instead?', $property));
        }

        $object = $zval[self::VALUE];
        $class = $object::class;
        $mutator = $this->getWriteInfo($class, $property, $value);

        try {
            if (PropertyWriteInfo::TYPE_NONE !== $mutator->getType()) {
                $type = $mutator->getType();

                if (PropertyWriteInfo::TYPE_METHOD === $type) {
                    $object->{$mutator->getName()}($value);
                } elseif (PropertyWriteInfo::TYPE_PROPERTY === $type) {
                    $object->{$mutator->getName()} = $value;
                } elseif (PropertyWriteInfo::TYPE_ADDER_AND_REMOVER === $type) {
                    $this->writeCollection($zval, $property, $value, $mutator->getAdderInfo(), $mutator->getRemoverInfo());
                }
            } elseif ($object instanceof \stdClass && property_exists($object, $property)) {
                $object->$property = $value;
            } elseif (!$this->ignoreInvalidProperty) {
                if ($mutator->hasErrors()) {
                    throw new NoSuchPropertyException(implode('. ', $mutator->getErrors()).'.');
                }

                throw new NoSuchPropertyException(\sprintf('Could not determine access type for property "%s" in class "%s".', $property, get_debug_type($object)));
            }
        } catch (\TypeError $e) {
            if ($recursive || !$value instanceof \DateTimeInterface || !\in_array($value::class, ['DateTime', 'DateTimeImmutable'], true) || __FILE__ !== ($e->getTrace()[0]['file'] ?? null)) {
                throw $e;
            }

            $value = $value instanceof \DateTimeImmutable ? \DateTime::createFromImmutable($value) : \DateTimeImmutable::createFromMutable($value);
            try {
                $this->writeProperty($zval, $property, $value, true);
            } catch (\TypeError) {
                throw $e; // throw the previous error
            }
        }
    }

    /**
     * Adjusts a collection-valued property by calling add*() and remove*() methods.
     */
    private function writeCollection(array $zval, string $property, iterable $collection, PropertyWriteInfo $addMethod, PropertyWriteInfo $removeMethod): void
    {
        // At this point the add and remove methods have been found
        $previousValue = $this->readProperty($zval, $property);
        $previousValue = $previousValue[self::VALUE];

        $removeMethodName = $removeMethod->getName();
        $addMethodName = $addMethod->getName();

        if ($previousValue instanceof \Traversable) {
            $previousValue = iterator_to_array($previousValue);
        }
        if ($previousValue && \is_array($previousValue)) {
            if (\is_object($collection)) {
                $collection = iterator_to_array($collection);