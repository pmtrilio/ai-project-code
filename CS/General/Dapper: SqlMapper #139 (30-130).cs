    public static partial class SqlMapper
    {
        private class PropertyInfoByNameComparer : IComparer<PropertyInfo>
        {
            public int Compare(PropertyInfo? x, PropertyInfo? y) => string.CompareOrdinal(x?.Name, y?.Name);
        }
        private static int GetColumnHash(DbDataReader reader, int startBound = 0, int length = -1)
        {
            unchecked
            {
                int max = length < 0 ? reader.FieldCount : startBound + length;
                int hash = (-37 * startBound) + max;
                for (int i = startBound; i < max; i++)
                {
                    object tmp = reader.GetName(i);
                    hash = (-79 * ((hash * 31) + (tmp?.GetHashCode() ?? 0))) + (reader.GetFieldType(i)?.GetHashCode() ?? 0);
                }
                return hash;
            }
        }

        /// <summary>
        /// Called if the query cache is purged via PurgeQueryCache
        /// </summary>
        public static event EventHandler? QueryCachePurged;
        private static void OnQueryCachePurged()
        {
            var handler = QueryCachePurged;
            handler?.Invoke(null, EventArgs.Empty);
        }

        private static readonly System.Collections.Concurrent.ConcurrentDictionary<Identity, CacheInfo> _queryCache = new();
        private static void SetQueryCache(Identity key, CacheInfo value)
        {
            if (Interlocked.Increment(ref collect) == COLLECT_PER_ITEMS)
            {
                CollectCacheGarbage();
            }
            _queryCache[key] = value;
        }

        private static void CollectCacheGarbage()
        {
            try
            {
                foreach (var pair in _queryCache)
                {
                    if (pair.Value.GetHitCount() <= COLLECT_HIT_COUNT_MIN)
                    {
                        _queryCache.TryRemove(pair.Key, out var _);
                    }
                }
            }

            finally
            {
                Interlocked.Exchange(ref collect, 0);
            }
        }

        private const int COLLECT_PER_ITEMS = 1000, COLLECT_HIT_COUNT_MIN = 0;
        private static int collect;

        private static bool TryGetQueryCache(Identity key, [NotNullWhen(true)] out CacheInfo? value)
        {
            if (_queryCache.TryGetValue(key, out value!))
            {
                value.RecordHit();
                return true;
            }
            value = null;
            return false;
        }

        /// <summary>
        /// Purge the query cache
        /// </summary>
        public static void PurgeQueryCache()
        {
            _queryCache.Clear();
            TypeDeserializerCache.Purge();
            OnQueryCachePurged();
        }

        private static void PurgeQueryCacheByType(Type type)
        {
            foreach (var entry in _queryCache)
            {
                if (entry.Key.Type == type)
                    _queryCache.TryRemove(entry.Key, out _);
            }
            TypeDeserializerCache.Purge(type);
        }

        /// <summary>
        /// Return a count of all the cached queries by Dapper
        /// </summary>
        /// <returns></returns>
        public static int GetCachedSQLCount()
        {
            return _queryCache.Count;