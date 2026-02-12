    public function save(CacheItemInterface $item): bool
    {
        if (!$item instanceof CacheItem) {
            return false;
        }
        $item = (array) $item;
        $key = $item["\0*\0key"];
        $value = $item["\0*\0value"];
        $expiry = $item["\0*\0expiry"];

        $now = $this->getCurrentTime();

        if (null !== $expiry) {
            if (!$expiry) {
                $expiry = \PHP_INT_MAX;
            } elseif ($expiry <= $now) {
                $this->deleteItem($key);

                return true;
            }
        }
        if ($this->storeSerialized && null === $value = $this->freeze($value, $key)) {
            return false;
        }
        if (null === $expiry && 0 < $this->defaultLifetime) {
            $expiry = $this->defaultLifetime;
            $expiry = $now + ($expiry > ($this->maxLifetime ?: $expiry) ? $this->maxLifetime : $expiry);
        } elseif ($this->maxLifetime && (null === $expiry || $expiry > $now + $this->maxLifetime)) {
            $expiry = $now + $this->maxLifetime;
        }

        if ($this->maxItems) {
            unset($this->values[$key], $this->tags[$key]);

            // Iterate items and vacuum expired ones while we are at it
            foreach ($this->values as $k => $v) {
                if ($this->expiries[$k] > $now && \count($this->values) < $this->maxItems) {
                    break;
                }

                unset($this->values[$k], $this->tags[$k], $this->expiries[$k]);
            }
        }

        $this->values[$key] = $value;
        $this->expiries[$key] = $expiry ?? \PHP_INT_MAX;

        if (null === $this->tags[$key] = $item["\0*\0newMetadata"][CacheItem::METADATA_TAGS] ?? null) {
            unset($this->tags[$key]);
        }

        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->save($item);
    }

    public function commit(): bool
    {
        return true;
    }

    public function clear(string $prefix = ''): bool
    {
        if ('' !== $prefix) {
            $now = $this->getCurrentTime();