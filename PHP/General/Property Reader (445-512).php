
                throw $e;
            }
        } elseif (property_exists($object, $property) && \array_key_exists($property, (array) $object)) {
            $result[self::VALUE] = $object->$property;
            if (isset($zval[self::REF])) {
                $result[self::REF] = &$object->$property;
            }
        } elseif ($isNullSafe) {
            $result[self::VALUE] = null;
        } elseif (!$ignoreInvalidProperty) {
            throw new NoSuchPropertyException(\sprintf('Can\'t get a way to read the property "%s" in class "%s".', $property, $class));
        }

        // Objects are always passed around by reference
        if (isset($zval[self::REF]) && \is_object($result[self::VALUE])) {
            $result[self::REF] = $result[self::VALUE];
        }

        return $result;
    }

    /**
     * Guesses how to read the property value.
     */
    private function getReadInfo(string $class, string $property): ?PropertyReadInfo
    {
        $key = str_replace('\\', '.', $class).'..'.$property;

        if (isset($this->readPropertyCache[$key])) {
            return $this->readPropertyCache[$key];
        }

        if ($this->cacheItemPool) {
            $item = $this->cacheItemPool->getItem(self::CACHE_PREFIX_READ.rawurlencode($key));
            if ($item->isHit()) {
                return $this->readPropertyCache[$key] = $item->get();
            }
        }

        $accessor = $this->readInfoExtractor->getReadInfo($class, $property, [
            'enable_getter_setter_extraction' => true,
            'enable_magic_methods_extraction' => $this->magicMethodsFlags,
            'enable_constructor_extraction' => false,
        ]);

        if (isset($item)) {
            $this->cacheItemPool->save($item->set($accessor));
        }

        return $this->readPropertyCache[$key] = $accessor;
    }

    /**
     * Sets the value of an index in a given array-accessible value.
     *
     * @throws NoSuchIndexException If the array does not implement \ArrayAccess or it is not an array
     */
    private function writeIndex(array $zval, string|int $index, mixed $value): void
    {
        if (!$zval[self::VALUE] instanceof \ArrayAccess && !\is_array($zval[self::VALUE])) {
            throw new NoSuchIndexException(\sprintf('Cannot modify index "%s" in object of type "%s" because it doesn\'t implement \ArrayAccess.', $index, get_debug_type($zval[self::VALUE])));
        }

        $zval[self::REF][$index] = $value;
    }

    /**