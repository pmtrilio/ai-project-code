    /**
     * Interact with the client's secret.
     */
    protected function secret(): Attribute
    {
        return Attribute::make(
            set: function (?string $value): ?string {
                $this->plainSecret = $value;

                return $this->castAttributeAsHashedString('secret', $value);
            },
        );
    }

    /**
     * Interact with the client's plain secret.
     */
    protected function plainSecret(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->plainSecret
        );
    }

    /**
     * Interact with the client's redirect URIs.
     */
    protected function redirectUris(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes): array => match (true) {
                ! empty($value) => $this->fromJson($value),
                ! empty($attributes['redirect']) => explode(',', $attributes['redirect']),
                default => [],
            },
        );
    }

    /**
     * Interact with the client's grant types.
     */
    protected function grantTypes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): array => isset($value) ? $this->fromJson($value) : array_keys(array_filter([
                'authorization_code' => ! empty($this->redirect_uris),
                'client_credentials' => $this->confidential() && $this->firstParty(),
                'implicit' => ! empty($this->redirect_uris),
                'password' => $this->password_client,
                'personal_access' => $this->personal_access_client && $this->confidential(),
                'refresh_token' => true,
                'urn:ietf:params:oauth:grant-type:device_code' => true,
            ])),
        );
    }

    /**
     * Determine if the client is a "first party" client.
     */
    public function firstParty(): bool
    {
        if (array_key_exists('user_id', $this->attributes)) {
            return empty($this->user_id);
        }

        return empty($this->owner_id);
    }