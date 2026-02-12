        foreach ($this->class->getPropertiesIterator() as $association) {
            if (! ($association instanceof ManyToManyAssociationMetadata)) {
                continue;
            }

            // @Todo this only covers scenarios with no inheritance or of the same level. Is there something
            // like self-referential relationship between different levels of an inheritance hierarchy? I hope not!
            $selfReferential   = $association->getTargetEntity() === $association->getSourceEntity();
            $owningAssociation = $association;
            $otherColumns      = [];
            $otherKeys         = [];
            $keys              = [];

            if (! $owningAssociation->isOwningSide()) {
                $class             = $this->em->getClassMetadata($association->getTargetEntity());
                $owningAssociation = $class->getProperty($association->getMappedBy());
            }

            $joinTable     = $owningAssociation->getJoinTable();
            $joinTableName = $joinTable->getQuotedQualifiedName($this->platform);
            $joinColumns   = $association->isOwningSide()
                ? $joinTable->getJoinColumns()
                : $joinTable->getInverseJoinColumns();

            if ($selfReferential) {
                $otherColumns = ! $association->isOwningSide()
                    ? $joinTable->getJoinColumns()
                    : $joinTable->getInverseJoinColumns();
            }

            $isOnDeleteCascade = false;

            foreach ($joinColumns as $joinColumn) {
                $keys[] = $this->platform->quoteIdentifier($joinColumn->getColumnName());

                if ($joinColumn->isOnDeleteCascade()) {
                    $isOnDeleteCascade = true;
                }
            }

            foreach ($otherColumns as $joinColumn) {
                $otherKeys[] = $this->platform->quoteIdentifier($joinColumn->getColumnName());

                if ($joinColumn->isOnDeleteCascade()) {
                    $isOnDeleteCascade = true;
                }
            }

            if ($isOnDeleteCascade) {
                continue;
            }

            $this->conn->delete($joinTableName, array_combine($keys, $identifier));

            if ($selfReferential) {
                $this->conn->delete($joinTableName, array_combine($otherKeys, $identifier));
            }
        }
    }

    /**
     * Prepares the data changeset of a managed entity for database insertion (initial INSERT).
     * The changeset of the entity is obtained from the currently running UnitOfWork.
     *
     * @param object $entity The entity for which to prepare the data.
     *
     * @return Query\Parameter[][] The prepared data for the tables to update.
     */
    protected function prepareInsertData($entity) : array
    {
        $unitOfWork = $this->em->getUnitOfWork();
        $changeSet  = $unitOfWork->getEntityChangeSet($entity);
        $result     = [];

        $tableName    = $this->class->getTableName();
        $columnPrefix = '';

        foreach ($changeSet as $propertyName => $propertyChangeSet) {
            $property = $this->class->getProperty($propertyName);