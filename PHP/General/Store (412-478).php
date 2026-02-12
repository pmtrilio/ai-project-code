    }

    /**
     * Generates a cache key for the given Request.
     *
     * This method should return a key that must only depend on a
     * normalized version of the request URI.
     *
     * If the same URI can have more than one representation, based on some
     * headers, use a Vary header to indicate them, and each representation will
     * be stored independently under the same cache key.
     */
    protected function generateCacheKey(Request $request): string
    {
        $key = $request->getUri();

        if ('QUERY' === $request->getMethod()) {
            // add null byte to separate the URI from the body and avoid boundary collisions
            // which could lead to cache poisoning
            $key .= "\0".$request->getContent();
        }

        return 'md'.hash('sha256', $key);
    }

    /**
     * Returns a cache key for the given Request.
     */
    private function getCacheKey(Request $request): string
    {
        if (isset($this->keyCache[$request])) {
            return $this->keyCache[$request];
        }

        return $this->keyCache[$request] = $this->generateCacheKey($request);
    }

    /**
     * Persists the Request HTTP headers.
     */
    private function persistRequest(Request $request): array
    {
        return $request->headers->all();
    }

    /**
     * Persists the Response HTTP headers.
     */
    private function persistResponse(Response $response): array
    {
        $headers = $response->headers->all();
        $headers['X-Status'] = [$response->getStatusCode()];

        return $headers;
    }

    /**
     * Restores a Response from the HTTP headers and body.
     */
    private function restoreResponse(array $headers, ?string $path = null): ?Response
    {
        $status = $headers['X-Status'][0];
        unset($headers['X-Status']);
        $content = null;

        if (null !== $path) {
            $headers['X-Body-File'] = [$path];