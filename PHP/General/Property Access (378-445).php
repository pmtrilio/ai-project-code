    /**
     * Reads the value of a property from an object.
     *
     * @throws NoSuchPropertyException If $ignoreInvalidProperty is false and the property does not exist or is not public
     */
    private function readProperty(array $zval, string $property, bool $ignoreInvalidProperty = false, bool $isNullSafe = false): array
    {
        if (!\is_object($zval[self::VALUE])) {
            throw new NoSuchPropertyException(\sprintf('Cannot read property "%s" from an array. Maybe you intended to write the property path as "[%1$s]" instead.', $property));
        }

        $result = self::RESULT_PROTO;
        $object = $zval[self::VALUE];
        $class = $object::class;
        $access = $this->getReadInfo($class, $property);

        if (null !== $access) {
            $name = $access->getName();
            $type = $access->getType();

            try {
                if (PropertyReadInfo::TYPE_METHOD === $type) {
                    try {
                        $result[self::VALUE] = $object->$name();
                    } catch (\TypeError $e) {
                        [$trace] = $e->getTrace();

                        // handle uninitialized properties in PHP >= 7
                        if (__FILE__ === ($trace['file'] ?? null)
                            && $name === $trace['function']
                            && $object instanceof $trace['class']
                            && preg_match('/Return value (?:of .*::\w+\(\) )?must be of (?:the )?type (\w+), null returned$/', $e->getMessage(), $matches)
                        ) {
                            throw new UninitializedPropertyException(\sprintf('The method "%s::%s()" returned "null", but expected type "%3$s". Did you forget to initialize a property or to make the return type nullable using "?%3$s"?', get_debug_type($object), $name, $matches[1]), 0, $e);
                        }

                        throw $e;
                    }
                } elseif (PropertyReadInfo::TYPE_PROPERTY === $type) {
                    if (!isset($object->$name) && !\array_key_exists($name, (array) $object)) {
                        try {
                            $r = new \ReflectionProperty($class, $name);

                            if ($r->isPublic() && !$r->hasType()) {
                                throw new UninitializedPropertyException(\sprintf('The property "%s::$%s" is not initialized.', $class, $name));
                            }
                        } catch (\ReflectionException $e) {
                            if (!$ignoreInvalidProperty) {
                                throw new NoSuchPropertyException(\sprintf('Can\'t get a way to read the property "%s" in class "%s".', $property, $class));
                            }
                        }
                    }

                    $result[self::VALUE] = $object->$name;

                    if (isset($zval[self::REF]) && $access->canBeReference()) {
                        $result[self::REF] = &$object->$name;
                    }
                }
            } catch (\Error $e) {
                // handle uninitialized properties in PHP >= 7.4
                if (preg_match('/^Typed property ([\w\\\\@]+)::\$(\w+) must not be accessed before initialization$/', $e->getMessage(), $matches) || preg_match('/^Cannot access uninitialized non-nullable property ([\w\\\\@]+)::\$(\w+) by reference$/', $e->getMessage(), $matches)) {
                    $r = new \ReflectionProperty(str_contains($matches[1], '@anonymous') ? $class : $matches[1], $matches[2]);
                    $type = ($type = $r->getType()) instanceof \ReflectionNamedType ? $type->getName() : (string) $type;

                    throw new UninitializedPropertyException(\sprintf('The property "%s::$%s" is not readable because it is typed "%s". You should initialize it or declare a default value instead.', $matches[1], $r->getName(), $type), 0, $e);
                }
