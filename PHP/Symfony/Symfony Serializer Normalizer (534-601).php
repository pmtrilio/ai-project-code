    {
        /**
         * @var callable|null
         */
        $callback = $context[self::CALLBACKS][$attribute] ?? $this->defaultContext[self::CALLBACKS][$attribute] ?? null;

        return $callback ? $callback($value, $object, $attribute, $format, $context) : $value;
    }

    final protected function applyFilterBool(\ReflectionParameter $parameter, mixed $value, array $context): mixed
    {
        if (!($context[self::FILTER_BOOL] ?? false)) {
            return $value;
        }

        if (!($parameterType = $parameter->getType()) instanceof \ReflectionNamedType || 'bool' !== $parameterType->getName()) {
            return $value;
        }

        return filter_var($value, \FILTER_VALIDATE_BOOL, \FILTER_NULL_ON_FAILURE) ?? $value;
    }

    /**
     * Computes the normalization context merged with current one. Metadata always wins over global context, as more specific.
     *
     * @internal
     */
    protected function getAttributeNormalizationContext(object $object, string $attribute, array $context): array
    {
        if (null === $metadata = $this->getAttributeMetadata($object, $attribute)) {
            return $context;
        }

        return array_merge($context, $metadata->getNormalizationContextForGroups($this->getGroups($context)));
    }

    /**
     * Computes the denormalization context merged with current one. Metadata always wins over global context, as more specific.
     *
     * @internal
     */
    protected function getAttributeDenormalizationContext(string $class, string $attribute, array $context): array
    {
        $context['deserialization_path'] = ($context['deserialization_path'] ?? false) ? $context['deserialization_path'].'.'.$attribute : $attribute;

        if (null === $metadata = $this->getAttributeMetadata($class, $attribute)) {
            return $context;
        }

        return array_merge($context, $metadata->getDenormalizationContextForGroups($this->getGroups($context)));
    }

    /**
     * @internal
     */
    protected function getAttributeMetadata(object|string $objectOrClass, string $attribute): ?AttributeMetadataInterface
    {
        if (!$this->classMetadataFactory) {
            return null;
        }

        return $this->classMetadataFactory->getMetadataFor($objectOrClass)->getAttributesMetadata()[$attribute] ?? null;
    }
}
