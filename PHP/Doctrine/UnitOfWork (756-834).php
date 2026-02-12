                case isset($this->scheduledForSynchronization[$className]):
                    $entitiesToProcess = $this->scheduledForSynchronization[$className];
                    break;

                default:
                    $entitiesToProcess = [];
            }

            foreach ($entitiesToProcess as $entity) {
                // Ignore uninitialized proxy objects
                if ($entity instanceof GhostObjectInterface && ! $entity->isProxyInitialized()) {
                    continue;
                }

                // Only MANAGED entities that are NOT SCHEDULED FOR INSERTION OR DELETION are processed here.
                $oid = spl_object_id($entity);

                if (! isset($this->entityInsertions[$oid]) && ! isset($this->entityDeletions[$oid]) && isset($this->entityStates[$oid])) {
                    $this->computeChangeSet($class, $entity);
                }
            }
        }
    }

    /**
     * Computes the changes of an association.
     *
     * @param AssociationMetadata $association The association mapping.
     * @param mixed               $value       The value of the association.
     *
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    private function computeAssociationChanges(AssociationMetadata $association, $value)
    {
        if ($value instanceof GhostObjectInterface && ! $value->isProxyInitialized()) {
            return;
        }

        if ($value instanceof PersistentCollection && $value->isDirty()) {
            $coid = spl_object_id($value);

            $this->collectionUpdates[$coid]  = $value;
            $this->visitedCollections[$coid] = $value;
        }

        // Look through the entities, and in any of their associations,
        // for transient (new) entities, recursively. ("Persistence by reachability")
        // Unwrap. Uninitialized collections will simply be empty.
        $unwrappedValue = $association instanceof ToOneAssociationMetadata ? [$value] : $value->unwrap();
        $targetEntity   = $association->getTargetEntity();
        $targetClass    = $this->em->getClassMetadata($targetEntity);

        foreach ($unwrappedValue as $key => $entry) {
            if (! ($entry instanceof $targetEntity)) {
                throw ORMInvalidArgumentException::invalidAssociation($targetClass, $association, $entry);
            }

            $state = $this->getEntityState($entry, self::STATE_NEW);

            if (! ($entry instanceof $targetEntity)) {
                throw UnexpectedAssociationValue::create(
                    $association->getSourceEntity(),
                    $association->getName(),
                    get_class($entry),
                    $targetEntity
                );
            }

            switch ($state) {
                case self::STATE_NEW:
                    if (! in_array('persist', $association->getCascade(), true)) {
                        $this->nonCascadedNewDetectedEntities[spl_object_id($entry)] = [$association, $entry];

                        break;
                    }

                    $this->persistNew($targetClass, $entry);
                    $this->computeChangeSet($targetClass, $entry);