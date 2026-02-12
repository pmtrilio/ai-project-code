            $castItem["\0*\0expiry"] = microtime(true) + $this->defaultLifetime;
        }

        if ($castItem["\0*\0poolHash"] === $this->poolHash && $castItem["\0*\0innerItem"]) {
            $innerItem = $castItem["\0*\0innerItem"];
        } elseif ($this->pool instanceof AdapterInterface) {
            // this is an optimization specific for AdapterInterface implementations
            // so we can save a round-trip to the backend by just creating a new item
            $innerItem = (self::$createCacheItem)($this->namespace.$castItem["\0*\0key"], null, $this->poolHash);
        } else {
            $innerItem = $this->pool->getItem($this->namespace.$castItem["\0*\0key"]);
        }

        (self::$setInnerItem)($innerItem, $item, $castItem["\0*\0expiry"]);

        return $this->pool->$method($innerItem);
    }

    private function generateItems(iterable $items): \Generator
    {
        $f = self::$createCacheItem;

        foreach ($items as $key => $item) {
            if ($this->namespaceLen) {
                $key = substr($key, $this->namespaceLen);
            }

            yield $key => $f($key, $item, $this->poolHash);
        }
    }

    private function getId(mixed $key): string
    {
        \assert('' !== CacheItem::validateKey($key));

        return $this->namespace.$key;
    }
}
