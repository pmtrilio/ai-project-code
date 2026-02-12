        return $this;
    }

    /**
     * Set the callback that should have an opportunity to modify the database query.
     *
     * @param  callable  $callback
     * @return $this
     */
    public function query($callback)
    {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Get the raw results of the search.
     *
     * @return mixed
     */
    public function raw()
    {
        return $this->engine()->search($this);
    }

    /**
     * Get the keys of search results.
     *
     * @return \Illuminate\Support\Collection
     */
    public function keys()
    {
        return $this->engine()->keys($this);
    }

    /**
     * Get the first result from the search.
     *
     * @return TModel
     */
    public function first()
    {
        return $this->get()->first();
    }

    /**
     * Get the results of the search.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TModel>
     */
    public function get()
    {
        return $this->engine()->get($this);
    }

    /**
     * Get the results of the search as a "lazy collection" instance.
     *
     * @return \Illuminate\Support\LazyCollection<int, TModel>
     */
    public function cursor()
    {
        return $this->engine()->cursor($this);
    }

    /**
     * Paginate the given query into a simple paginator.