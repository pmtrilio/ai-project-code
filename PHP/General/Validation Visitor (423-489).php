            $context->markGroupAsValidated($cacheKey, $groupHash);

            // Replace the "Default" group by the group sequence defined
            // for the class, if applicable.
            // This is done after checking the cache, so that
            // spl_object_hash() isn't called for this sequence and
            // "Default" is used instead in the cache. This is useful
            // if the getters below return different group sequences in
            // every call.
            if (Constraint::DEFAULT_GROUP === $group) {
                if ($metadata->hasGroupSequence()) {
                    // The group sequence is statically defined for the class
                    $group = $metadata->getGroupSequence();
                    $defaultOverridden = true;
                } elseif ($metadata->isGroupSequenceProvider()) {
                    if (null !== $provider = $metadata->getGroupProvider()) {
                        if (null === $this->groupProviderLocator) {
                            throw new \LogicException('A group provider locator is required when using group provider.');
                        }

                        $group = $this->groupProviderLocator->get($provider)->getGroups($object);
                    } else {
                        // The group sequence is dynamically obtained from the validated
                        // object
                        /** @var GroupSequenceProviderInterface $object */
                        $group = $object->getGroupSequence();
                    }
                    $defaultOverridden = true;

                    if (!$group instanceof GroupSequence) {
                        $group = new GroupSequence($group);
                    }
                }
            }

            // If the groups (=[<G1,G2>,G3,G4]) contain a group sequence
            // (=<G1,G2>), then call validateClassNode() with each entry of the
            // group sequence and abort if necessary (G1, G2)
            if ($group instanceof GroupSequence) {
                $this->stepThroughGroupSequence(
                    $object,
                    $object,
                    $cacheKey,
                    $metadata,
                    $propertyPath,
                    $traversalStrategy,
                    $group,
                    $defaultOverridden ? Constraint::DEFAULT_GROUP : null,
                    $context
                );

                // Skip the group sequence when validating properties, because
                // stepThroughGroupSequence() already validates the properties
                unset($groups[$key]);

                continue;
            }

            $this->validateInGroup($object, $cacheKey, $metadata, $group, $context);
        }

        // If no more groups should be validated for the property nodes,
        // we can safely quit
        if (0 === \count($groups)) {
            return;
        }
