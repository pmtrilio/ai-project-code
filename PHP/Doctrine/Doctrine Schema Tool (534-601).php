            switch (true) {
                case $property instanceof ToOneAssociationMetadata:
                    $primaryKeyColumns = []; // PK is unnecessary for this relation-type

                    $this->gatherRelationJoinColumns(
                        $property->getJoinColumns(),
                        $table,
                        $foreignClass,
                        $property,
                        $primaryKeyColumns,
                        $addedFks,
                        $blacklistedFks
                    );

                    break;

                case $property instanceof OneToManyAssociationMetadata:
                    //... create join table, one-many through join table supported later
                    throw NotSupported::create();

                case $property instanceof ManyToManyAssociationMetadata:
                    // create join table
                    $joinTable     = $property->getJoinTable();
                    $joinTableName = $joinTable->getQuotedQualifiedName($this->platform);
                    $theJoinTable  = $schema->createTable($joinTableName);

                    $primaryKeyColumns = [];

                    // Build first FK constraint (relation table => source table)
                    $this->gatherRelationJoinColumns(
                        $joinTable->getJoinColumns(),
                        $theJoinTable,
                        $class,
                        $property,
                        $primaryKeyColumns,
                        $addedFks,
                        $blacklistedFks
                    );

                    // Build second FK constraint (relation table => target table)
                    $this->gatherRelationJoinColumns(
                        $joinTable->getInverseJoinColumns(),
                        $theJoinTable,
                        $foreignClass,
                        $property,
                        $primaryKeyColumns,
                        $addedFks,
                        $blacklistedFks
                    );

                    $theJoinTable->setPrimaryKey($primaryKeyColumns);

                    break;
            }
        }
    }

    /**
     * Gets the class metadata that is responsible for the definition of the referenced column name.
     *
     * Previously this was a simple task, but with DDC-117 this problem is actually recursive. If its
     * not a simple field, go through all identifier field names that are associations recursively and
     * find that referenced column name.
     *
     * TODO: Is there any way to make this code more pleasing?
     *
     * @param ClassMetadata $class
     * @param string        $referencedColumnName