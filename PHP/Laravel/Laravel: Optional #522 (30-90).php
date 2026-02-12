    }

    /**
     * Dynamically access a property on the underlying object.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (is_object($this->value)) {
            return $this->value->{$key} ?? null;
        }
    }

    /**
     * Dynamically check a property exists on the underlying object.
     *
     * @param  mixed  $name
     * @return bool
     */
    public function __isset($name)
    {
        if (is_object($this->value)) {
            return isset($this->value->{$name});
        }

        if (is_array($this->value) || $this->value instanceof ArrayObject) {
            return isset($this->value[$name]);
        }

        return false;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return Arr::accessible($this->value) && Arr::exists($this->value, $key);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return Arr::get($this->value, $key);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value