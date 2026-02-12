            foreach ($data as $key => $data) {
                // Ah this is the magic @ attribute types.
                if (str_starts_with($key, '@') && $this->isElementNameValid($attributeName = substr($key, 1))) {
                    if (!\is_scalar($data)) {
                        $data = $this->serializer->normalize($data, $format, $context);
                    }
                    if (\is_bool($data)) {
                        $data = (int) $data;
                    }

                    if ($context[self::IGNORE_EMPTY_ATTRIBUTES] ?? $this->defaultContext[self::IGNORE_EMPTY_ATTRIBUTES]) {
                        if (null === $data || '' === $data) {
                            continue;
                        }
                    }

                    $parentNode->setAttribute($attributeName, $data);
                } elseif ('#' === $key) {
                    $append = $this->selectNodeType($parentNode, $data, $format, $context);
                } elseif ('#comment' === $key) {
                    if (!\in_array(\XML_COMMENT_NODE, $encoderIgnoredNodeTypes, true)) {
                        $append = $this->appendComment($parentNode, $data);
                    }
                } elseif (\is_array($data) && !is_numeric($key)) {
                    // Is this array fully numeric keys?
                    if (!$preserveNumericKeys && $data && null === array_find_key($data, static fn ($v, $k) => \is_string($k))) {
                        /*
                         * Create nodes to append to $parentNode based on the $key of this array
                         * Produces <xml><item>0</item><item>1</item></xml>
                         * From ["item" => [0,1]];.
                         */
                        foreach ($data as $subData) {
                            $append = $this->appendNode($parentNode, $subData, $format, $context, $key);
                        }
                    } else {
                        $append = $this->appendNode($parentNode, $data, $format, $context, $key);
                    }
                } elseif (is_numeric($key) || !$this->isElementNameValid($key)) {
                    $append = $this->appendNode($parentNode, $data, $format, $context, 'item', $key);
                } elseif (null !== $data || !$removeEmptyTags) {
                    $append = $this->appendNode($parentNode, $data, $format, $context, $key);
                }
            }

            return $append;
        }

        if (\is_object($data)) {
            if (null === $this->serializer) {
                throw new BadMethodCallException(\sprintf('The serializer needs to be set to allow "%s()" to be used with object data.', __METHOD__));
            }

            $data = $this->serializer->normalize($data, $format, $context);
            if (null !== $data && !\is_scalar($data)) {
                return $this->buildXml($parentNode, $data, $format, $context, $xmlRootNodeName);
            }

            // top level data object was normalized into a scalar
            if (!$parentNode->parentNode->parentNode) {
                $root = $parentNode->parentNode;
                $root->removeChild($parentNode);

                return $this->appendNode($root, $data, $format, $context, $xmlRootNodeName);
            }

            return $this->appendNode($parentNode, $data, $format, $context, 'data');
        }
