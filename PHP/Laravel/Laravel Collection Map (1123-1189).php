    {
        return Arr::pull($this->items, $key, $default);
    }

    /**
     * Put an item in the collection by key.
     *
     * @param  TKey  $key
     * @param  TValue  $value
     * @return $this
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    /**
     * Get one or a specified number of items randomly from the collection.
     *
     * @param  (callable(self<TKey, TValue>): int)|int|null  $number
     * @param  bool  $preserveKeys
     * @return ($number is null ? TValue : static<int, TValue>)
     *
     * @throws \InvalidArgumentException
     */
    public function random($number = null, $preserveKeys = false)
    {
        if (is_null($number)) {
            return Arr::random($this->items);
        }

        if (is_callable($number)) {
            return new static(Arr::random($this->items, $number($this), $preserveKeys));
        }

        return new static(Arr::random($this->items, $number, $preserveKeys));
    }

    /**
     * Replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replace($items)
    {
        return new static(array_replace($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Recursively replace the collection items with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function replaceRecursive($items)
    {
        return new static(array_replace_recursive($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Reverse items order.
     *
     * @return static
     */