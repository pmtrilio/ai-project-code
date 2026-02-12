     *
     * @param bool $bool
     *
     * @return Query This query instance.
     */
    public function useQueryCache($bool)
    {
        $this->useQueryCache = $bool;

        return $this;
    }

    /**
     * Returns the cache driver used for query caching.
     *
     * @return Cache|null The cache driver used for query caching or NULL, if
     *                                           this Query does not use query caching.
     */
    public function getQueryCacheDriver()
    {
        if ($this->queryCache) {
            return $this->queryCache;
        }

        return $this->em->getConfiguration()->getQueryCacheImpl();
    }

    /**
     * Defines how long the query cache will be active before expire.
     *
     * @param int $timeToLive How long the cache entry is valid.
     *
     * @return Query This query instance.
     */
    public function setQueryCacheLifetime($timeToLive)
    {
        if ($timeToLive !== null) {
            $timeToLive = (int) $timeToLive;
        }

        $this->queryCacheTTL = $timeToLive;

        return $this;
    }

    /**
     * Retrieves the lifetime of resultset cache.
     *
     * @return int
     */
    public function getQueryCacheLifetime()
    {
        return $this->queryCacheTTL;
    }

    /**
     * Defines if the query cache is active or not.
     *
     * @param bool $expire Whether or not to force query cache expiration.
     *
     * @return Query This query instance.
     */
    public function expireQueryCache($expire = true)
    {
        $this->expireQueryCache = $expire;

        return $this;
    }