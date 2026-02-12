
        return $this;
    }

    public function forgetTranslation(string $key, string $locale): self
    {
        $translations = $this->getTranslations($key);

        unset(
            $translations[$locale],
            $this->$key
        );

        $this->setTranslations($key, $translations);

        return $this;
    }

    public function forgetTranslations(string $key, bool $asNull = false): self
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        collect($this->getTranslatedLocales($key))->each(function (string $locale) use ($key) {
            $this->forgetTranslation($key, $locale);
        });

        if ($asNull) {
            $this->attributes[$key] = null;
        }

        return $this;
    }

    public function forgetAllTranslations(string $locale): self
    {
        collect($this->getTranslatableAttributes())->each(function (string $attribute) use ($locale) {
            $this->forgetTranslation($attribute, $locale);
        });

        return $this;
    }

    public function getTranslatedLocales(string $key): array
    {
        return array_keys($this->getTranslations($key));
    }

    public function isNestedKey(string $key): bool
    {
        return str_contains($key, '->');
    }

    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes());
    }

    public function hasTranslation(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?: $this->getLocale();

        return isset($this->getTranslations($key)[$locale]);
    }

    public function replaceTranslations(string $key, array $translations): self
    {
        foreach ($this->getTranslatedLocales($key) as $locale) {