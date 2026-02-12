        return $this->hints[$name] ?? false;
    }

    /**
     * Check if the query has a hint
     *
     * @param string $name The name of the hint
     *
     * @return bool False if the query does not have any hint
     */
    public function hasHint($name)
    {
        return isset($this->hints[$name]);
    }

    /**
     * Return the key value map of query hints that are currently set.
     *
     * @return mixed[]
     */
    public function getHints()
    {
        return $this->hints;
    }

    /**
     * Executes the query and returns an IterableResult that can be used to incrementally
     * iterate over the result.
     *
     * @param ArrayCollection|mixed[]|null $parameters    The query parameters.
     * @param string|int|null              $hydrationMode The hydration mode to use.
     *
     * @return IterableResult
     */
    public function iterate($parameters = null, $hydrationMode = null)
    {
        if ($hydrationMode !== null) {
            $this->setHydrationMode($hydrationMode);
        }

        if (! empty($parameters)) {
            $this->setParameters($parameters);
        }

        $rsm  = $this->getResultSetMapping();
        $stmt = $this->doExecute();

        return $this->em->newHydrator($this->hydrationMode)->iterate($stmt, $rsm, $this->hints);
    }

    /**
     * Executes the query.
     *
     * @param ArrayCollection|mixed[]|null $parameters    Query parameters.
     * @param string|int|null              $hydrationMode Processing mode to be used during the hydration process.
     *
     * @return mixed
     */
    public function execute($parameters = null, $hydrationMode = null)
    {
        return $this->isCacheEnabled()
            ? $this->executeUsingQueryCache($parameters, $hydrationMode)
            : $this->executeIgnoreQueryCache($parameters, $hydrationMode);
    }

    /**
     * Execute query ignoring second level cache.
     *