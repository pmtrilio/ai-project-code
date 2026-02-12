        $port = isset($url['port']) ? ':'.$url['port'] : '';
        $user = $url['user'] ?? '';
        $pass = isset($url['pass']) ? ':'.$url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $url['path'] ?? '';
        $query = $url['query'] ? '?'.$url['query'] : '';
        $fragment = isset($url['fragment']) ? '#'.$url['fragment'] : '';

        return $scheme.$user.$pass.$host.$port.$path.$query.$fragment;
    }

    private function getExpirationTime(\DateTimeInterface|\DateInterval|int $expiration): string
    {
        if ($expiration instanceof \DateTimeInterface) {
            return $expiration->format('U');
        }

        if ($expiration instanceof \DateInterval) {
            return $this->now()->add($expiration)->format('U');
        }

        return (string) $expiration;
    }

    private function now(): \DateTimeImmutable
    {
        return $this->clock?->now() ?? \DateTimeImmutable::createFromFormat('U', time());
    }

    /**
     * @return self::STATUS_*
     */
    private function doVerify(string $uri): int
    {
        $url = parse_url($uri);
        $params = [];

        if (isset($url['query'])) {
            parse_str($url['query'], $params);
        }

        if (empty($params[$this->hashParameter])) {
            return self::STATUS_MISSING;
        }

        $hash = $params[$this->hashParameter];
        unset($params[$this->hashParameter]);

        if (!hash_equals($this->computeHash($this->buildUrl($url, $params)), strtr(rtrim($hash, '='), ['/' => '_', '+' => '-']))) {
            return self::STATUS_INVALID;
        }

        if (!$expiration = $params[$this->expirationParameter] ?? false) {
            return self::STATUS_VALID;
        }
