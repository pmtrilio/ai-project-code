    {
        return new self(sprintf("The mapping of field '%s' requires an the 'joinTable' attribute.", $fieldName));
    }

    /**
     * Called if a required option was not found but is required
     *
     * @param string $field          Which field cannot be processed?
     * @param string $expectedOption Which option is required
     * @param string $hint           Can optionally be used to supply a tip for common mistakes,
     *                               e.g. "Did you think of the plural s?"
     *
     * @return MappingException
     */
    public static function missingRequiredOption($field, $expectedOption, $hint = '')
    {
        $message = sprintf("The mapping of field '%s' is invalid: The option '%s' is required.", $field, $expectedOption);

        if (! empty($hint)) {
            $message .= ' (Hint: ' . $hint . ')';
        }

        return new self($message);
    }

    /**
     * Generic exception for invalid mappings.
     *
     * @param string $fieldName
     *
     * @return MappingException
     */
    public static function invalidMapping($fieldName)
    {
        return new self(sprintf("The mapping of field '%s' is invalid.", $fieldName));
    }

    /**
     * Exception for reflection exceptions - adds the entity name,
     * because there might be long classnames that will be shortened
     * within the stacktrace
     *
     * @param string $entity The entity's name
     *
     * @return MappingException
     */
    public static function reflectionFailure($entity, ReflectionException $previousException)
    {
        return new self('An error occurred in ' . $entity, 0, $previousException);
    }

    /**
     * @param string $className
     * @param string $joinColumn
     *
     * @return MappingException
     */
    public static function joinColumnMustPointToMappedField($className, $joinColumn)
    {
        return new self('The column ' . $joinColumn . ' must be mapped to a field in class '
            . $className . ' since it is referenced by a join column of another class.');
    }

    /**
     * @param string $className
     *
     * @return MappingException
     */