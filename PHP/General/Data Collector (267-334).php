    /**
     * @return string|resource
     */
    public function getContent()
    {
        return $this->data['content'];
    }

    public function isJsonRequest(): bool
    {
        return 1 === preg_match('{^application/(?:\w+\++)*json$}i', $this->data['request_headers']['content-type']);
    }

    public function getPrettyJson(): ?string
    {
        $decoded = json_decode($this->getContent());

        return \JSON_ERROR_NONE === json_last_error() ? json_encode($decoded, \JSON_PRETTY_PRINT) : null;
    }

    public function getContentType(): string
    {
        return $this->data['content_type'];
    }

    public function getStatusText(): string
    {
        return $this->data['status_text'];
    }

    public function getStatusCode(): int
    {
        return $this->data['status_code'];
    }

    public function getFormat(): string
    {
        return $this->data['format'];
    }

    public function getLocale(): string
    {
        return $this->data['locale'];
    }

    public function getDotenvVars(): ParameterBag
    {
        return new ParameterBag($this->data['dotenv_vars']->getValue());
    }

    /**
     * Gets the route name.
     *
     * The _route request attributes is automatically set by the Router Matcher.
     */
    public function getRoute(): string
    {
        return $this->data['route'];
    }

    public function getIdentifier(): string
    {
        return $this->data['identifier'];
    }

    /**
     * Gets the route parameters.
     *