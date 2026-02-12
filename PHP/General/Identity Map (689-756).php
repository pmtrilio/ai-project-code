     *
     * @param string $fieldName
     *
     * @return bool TRUE if the field is inherited, FALSE otherwise.
     */
    public function isInheritedProperty($fieldName)
    {
        $declaringClass = $this->properties[$fieldName]->getDeclaringClass();

        return $declaringClass->className !== $this->className;
    }

    /**
     * {@inheritdoc}
     */
    public function setTable(TableMetadata $table) : void
    {
        $this->table = $table;

        // Make sure inherited and declared properties reflect newly defined table
        foreach ($this->properties as $property) {
            switch (true) {
                case $property instanceof FieldMetadata:
                    $property->setTableName($property->getTableName() ?? $table->getName());
                    break;

                case $property instanceof ToOneAssociationMetadata:
                    // Resolve association join column table names
                    foreach ($property->getJoinColumns() as $joinColumn) {
                        /** @var JoinColumnMetadata $joinColumn */
                        $joinColumn->setTableName($joinColumn->getTableName() ?? $table->getName());
                    }

                    break;
            }
        }
    }

    /**
     * Checks whether the given type identifies an inheritance type.
     *
     * @param int $type
     *
     * @return bool TRUE if the given type identifies an inheritance type, FALSe otherwise.
     */
    private function isInheritanceType($type)
    {
        return $type === InheritanceType::NONE
            || $type === InheritanceType::SINGLE_TABLE
            || $type === InheritanceType::JOINED
            || $type === InheritanceType::TABLE_PER_CLASS;
    }

    public function getColumn(string $columnName) : ?ColumnMetadata
    {
        foreach ($this->properties as $property) {
            switch (true) {
                case $property instanceof FieldMetadata:
                    if ($property->getColumnName() === $columnName) {
                        return $property;
                    }

                    break;

                case $property instanceof ToOneAssociationMetadata:
                    foreach ($property->getJoinColumns() as $joinColumn) {
                        if ($joinColumn->getColumnName() === $columnName) {
                            return $joinColumn;