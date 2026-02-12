    abstract protected function doFetch($id);

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    abstract protected function doContains($id);

    /**
     * Default implementation of doSaveMultiple. Each driver that supports multi-put should override it.
     *
     * @param mixed[] $keysAndValues Array of keys and values to save in cache
     * @param int     $lifetime      The lifetime. If != 0, sets a specific lifetime for these
     *                               cache entries (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $success = true;

        foreach ($keysAndValues as $key => $value) {
            if ($this->doSave($key, $value, $lifetime)) {
                continue;
            }

            $success = false;
        }

        return $success;
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param string $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    abstract protected function doSave($id, $data, $lifeTime = 0);

    /**
     * Default implementation of doDeleteMultiple. Each driver that supports multi-delete should override it.
     *
     * @param string[] $keys Array of keys to delete from cache
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't
     */
    protected function doDeleteMultiple(array $keys)
    {
        $success = true;

        foreach ($keys as $key) {
            if ($this->doDelete($key)) {
                continue;
            }

            $success = false;
        }

        return $success;
    }