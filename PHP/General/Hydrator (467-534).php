                    if (isset($this->rsm->indexByMap[$dqlAlias])) {
                        $resultKey = $row[$this->rsm->indexByMap[$dqlAlias]];

                        if (isset($this->hints['collection'])) {
                            $this->hints['collection']->hydrateSet($resultKey, $element);
                        }

                        $result[$resultKey] = $element;
                    } else {
                        $resultKey = $this->resultCounter;
                        ++$this->resultCounter;

                        if (isset($this->hints['collection'])) {
                            $this->hints['collection']->hydrateAdd($element);
                        }

                        $result[] = $element;
                    }

                    $this->identifierMap[$dqlAlias][$id[$dqlAlias]] = $resultKey;

                    // Update result pointer
                    $this->resultPointers[$dqlAlias] = $element;
                } else {
                    // Update result pointer
                    $index                           = $this->identifierMap[$dqlAlias][$id[$dqlAlias]];
                    $this->resultPointers[$dqlAlias] = $result[$index];
                    $resultKey                       = $index;
                }
            }

            if (isset($this->hints[Query::HINT_INTERNAL_ITERATION]) && $this->hints[Query::HINT_INTERNAL_ITERATION]) {
                $this->uow->hydrationComplete();
            }
        }

        if (! isset($resultKey)) {
            $this->resultCounter++;
        }

        // Append scalar values to mixed result sets
        if (isset($rowData['scalars'])) {
            if (! isset($resultKey)) {
                $resultKey = isset($this->rsm->indexByMap['scalars'])
                    ? $row[$this->rsm->indexByMap['scalars']]
                    : $this->resultCounter - 1;
            }

            foreach ($rowData['scalars'] as $name => $value) {
                $result[$resultKey][$name] = $value;
            }
        }

        // Append new object to mixed result sets
        if (isset($rowData['newObjects'])) {
            if (! isset($resultKey)) {
                $resultKey = $this->resultCounter - 1;
            }

            $hasNoScalars = ! (isset($rowData['scalars']) && $rowData['scalars']);

            foreach ($rowData['newObjects'] as $objIndex => $newObject) {
                $class = $newObject['class'];
                $args  = $newObject['args'];
                $obj   = $class->newInstanceArgs($args);

                if ($hasNoScalars && count($rowData['newObjects']) === 1) {
                    $result[$resultKey] = $obj;