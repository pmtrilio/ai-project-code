        $getsetter = lcfirst($camelized);
        $getsetterNonCamelized = lcfirst($nonCamelized);

        if ($allowGetterSetter) {
            [$accessible, $methodAccessibleErrors] = $this->isMethodAccessible($reflClass, $getsetter, 1);
            if ($accessible) {
                $method = $reflClass->getMethod($getsetter);

                return new PropertyWriteInfo(PropertyWriteInfo::TYPE_METHOD, $getsetter, $this->getWriteVisibilityForMethod($method), $method->isStatic());
            }

            $errors[] = $methodAccessibleErrors;

            if ($getsetter !== $getsetterNonCamelized) {
                [$accessible, $methodAccessibleErrors] = $this->isMethodAccessible($reflClass, $getsetterNonCamelized, 1);
                if ($accessible) {
                    $method = $reflClass->getMethod($getsetterNonCamelized);

                    return new PropertyWriteInfo(PropertyWriteInfo::TYPE_METHOD, $getsetterNonCamelized, $this->getWriteVisibilityForMethod($method), $method->isStatic());
                }
                $errors[] = $methodAccessibleErrors;
            }
        }

        if ($reflClass->hasProperty($property) && ($reflClass->getProperty($property)->getModifiers() & $this->propertyReflectionFlags)) {
            $reflProperty = $reflClass->getProperty($property);
            if (!$reflProperty->isReadOnly()) {
                return new PropertyWriteInfo(PropertyWriteInfo::TYPE_PROPERTY, $property, $this->getWriteVisibilityForProperty($reflProperty), $reflProperty->isStatic());
            }

            $errors[] = [\sprintf('The property "%s" in class "%s" is a promoted readonly property.', $property, $reflClass->getName())];
            $allowMagicSet = $allowMagicCall = false;
        }

        if ($allowMagicSet) {
            [$accessible, $methodAccessibleErrors] = $this->isMethodAccessible($reflClass, '__set', 2);
            if ($accessible) {
                return new PropertyWriteInfo(PropertyWriteInfo::TYPE_PROPERTY, $property, PropertyWriteInfo::VISIBILITY_PUBLIC, false);
            }

            $errors[] = $methodAccessibleErrors;
        }

        if ($allowMagicCall) {
            [$accessible, $methodAccessibleErrors] = $this->isMethodAccessible($reflClass, '__call', 2);
            if ($accessible) {
                return new PropertyWriteInfo(PropertyWriteInfo::TYPE_METHOD, 'set'.$camelized, PropertyWriteInfo::VISIBILITY_PUBLIC, false);
            }

            $errors[] = $methodAccessibleErrors;
        }

        if (!$allowAdderRemover && null !== $adderAccessName && null !== $removerAccessName) {
            $errors[] = [\sprintf(
                'The property "%s" in class "%s" can be defined with the methods "%s()" but '.
                'the new value must be an array or an instance of \Traversable',
                $property,
                $reflClass->getName(),
                implode('()", "', [$adderAccessName, $removerAccessName])
            )];
        }

        $noneProperty = new PropertyWriteInfo();
        $noneProperty->setErrors(array_merge([], ...$errors));

        return $noneProperty;
    }