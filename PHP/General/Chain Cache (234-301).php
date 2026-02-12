    }

    public function save(CacheItemInterface $item): bool
    {
        $saved = true;
        $i = $this->adapterCount;

        while ($i--) {
            $saved = $this->adapters[$i]->save($item) && $saved;
        }

        return $saved;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $saved = true;
        $i = $this->adapterCount;

        while ($i--) {
            $saved = $this->adapters[$i]->saveDeferred($item) && $saved;
        }

        return $saved;
    }

    public function commit(): bool
    {
        $committed = true;
        $i = $this->adapterCount;

        while ($i--) {
            $committed = $this->adapters[$i]->commit() && $committed;
        }

        return $committed;
    }

    public function prune(): bool
    {
        $pruned = true;

        foreach ($this->adapters as $adapter) {
            if ($adapter instanceof PruneableInterface) {
                $pruned = $adapter->prune() && $pruned;
            }
        }

        return $pruned;
    }

    public function withSubNamespace(string $namespace): static
    {
        $clone = clone $this;
        $adapters = [];

        foreach ($this->adapters as $adapter) {
            if (!$adapter instanceof NamespacedPoolInterface) {
                throw new BadMethodCallException('All adapters must implement NamespacedPoolInterface to support namespaces.');
            }

            $adapters[] = $adapter->withSubNamespace($namespace);
        }
        $clone->adapters = $adapters;

        return $clone;
    }
