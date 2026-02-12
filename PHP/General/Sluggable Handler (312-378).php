    {
        $includeTrashed = $config['includeTrashed'];

        $query = $this->model->newQuery()
            ->findSimilarSlugs($attribute, $config, $slug);

        // use the model scope to find similar slugs
        $query->withUniqueSlugConstraints($this->model, $attribute, $config, $slug);

        // include trashed models if required
        if ($includeTrashed && $this->usesSoftDeleting()) {
            $query->withTrashed();
        }

        // get the list of all matching slugs
        $results = $query
            ->withoutEagerLoads()
            ->select([$attribute, $this->model->getQualifiedKeyName()])
            ->get()
            ->toBase();

        // key the results and return
        return $results->pluck($attribute, $this->model->getKeyName());
    }

    /**
     * Does this model use softDeleting?
     */
    protected function usesSoftDeleting(): bool
    {
        return method_exists($this->model, 'bootSoftDeletes');
    }

    /**
     * Generate a unique slug for a given string.
     *
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public static function createSlug(Model|string $model, string $attribute, string $fromString, ?array $config = null): string
    {
        if (is_string($model)) {
            $model = new $model();
        }

        $instance = (new static())->setModel($model);

        if ($config === null) {
            $config = Arr::get($model->sluggable(), $attribute);
            if ($config === null) {
                $modelClass = get_class($model);

                throw new \InvalidArgumentException("Argument 2 passed to SlugService::createSlug ['{$attribute}'] is not a valid slug attribute for model {$modelClass}.");
            }
        }

        $config = $instance->getConfiguration($config);

        $slug = $instance->generateSlug($fromString, $config, $attribute);
        $slug = $instance->validateSlug($slug, $config, $attribute);
        $slug = $instance->makeSlugUnique($slug, $config, $attribute);

        return $slug;
    }

    /**
     * @return $this