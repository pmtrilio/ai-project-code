
            if (isset($this->metadata[$domain][$key])) {
                return $this->metadata[$domain][$key];
            }
        }

        return null;
    }

    public function setMetadata(string $key, mixed $value, string $domain = 'messages'): void
    {
        $this->metadata[$domain][$key] = $value;
    }

    public function deleteMetadata(string $key = '', string $domain = 'messages'): void
    {
        if ('' == $domain) {
            $this->metadata = [];
        } elseif ('' == $key) {
            unset($this->metadata[$domain]);
        } else {
            unset($this->metadata[$domain][$key]);
        }
    }

    public function getCatalogueMetadata(string $key = '', string $domain = 'messages'): mixed
    {
        if (!$domain) {
            return $this->catalogueMetadata;
        }

        if (isset($this->catalogueMetadata[$domain])) {
            if (!$key) {
                return $this->catalogueMetadata[$domain];
            }

            if (isset($this->catalogueMetadata[$domain][$key])) {
                return $this->catalogueMetadata[$domain][$key];
            }
        }

        return null;
    }

    public function setCatalogueMetadata(string $key, mixed $value, string $domain = 'messages'): void
    {
        $this->catalogueMetadata[$domain][$key] = $value;
    }

    public function deleteCatalogueMetadata(string $key = '', string $domain = 'messages'): void
    {
        if (!$domain) {
            $this->catalogueMetadata = [];
        } elseif (!$key) {
            unset($this->catalogueMetadata[$domain]);
        } else {
            unset($this->catalogueMetadata[$domain][$key]);
        }
    }

    /**
     * Adds current values with the new values.
     *
     * @param array $values Values to add
     */
    private function addMetadata(array $values): void
    {
        foreach ($values as $domain => $keys) {