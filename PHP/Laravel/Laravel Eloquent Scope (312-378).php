            if (in_array($this->model->getKeyType(), ['int', 'integer'])) {
                $this->query->whereIntegerNotInRaw($this->model->getQualifiedKeyName(), $id);
            } else {
                $this->query->whereNotIn($this->model->getQualifiedKeyName(), $id);
            }

            return $this;
        }

        if ($id !== null && $this->model->getKeyType() === 'string') {
            $id = (string) $id;
        }

        return $this->where($this->model->getQualifiedKeyName(), '!=', $id);
    }

    /**
     * Exclude the given models from the query results.
     *
     * @param  iterable|mixed  $models
     * @return static
     */
    public function except($models)
    {
        return $this->whereKeyNot(
            $models instanceof Model
                ? $models->getKey()
                : Collection::wrap($models)->modelKeys()
        );
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  (\Closure(static): mixed)|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof Closure && is_null($operator)) {
            $column($query = $this->model->newQueryWithoutRelationships());

            $this->eagerLoad = array_merge($this->eagerLoad, $query->getEagerLoads());

            $this->withoutGlobalScopes(
                $query->removedScopes()
            );
            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            $this->query->where(...func_get_args());
        }

        return $this;
    }

    /**
     * Add a basic where clause to the query, and return the first result.
     *
     * @param  (\Closure(static): mixed)|string|array|\Illuminate\Contracts\Database\Query\Expression  $column
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  string  $boolean
     * @return TModel|null
     */