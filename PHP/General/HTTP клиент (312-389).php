                $pushedResponse = null;
            }
        }

        if (!$pushedResponse) {
            $ch = curl_init();
            $this->logger?->info(\sprintf('Request: "%s %s"', $method, $url));
            $curlopts += [\CURLOPT_SHARE => ($options['extra']['use_persistent_connections'] ?? false) ? $this->multi->share : $this->multi->persistentShare];
        }

        foreach ($curlopts as $opt => $value) {
            if (\PHP_INT_SIZE === 8 && \defined('CURLOPT_INFILESIZE_LARGE') && \CURLOPT_INFILESIZE === $opt && $value >= 1 << 31) {
                $opt = \CURLOPT_INFILESIZE_LARGE;
            }
            if (null !== $value && !curl_setopt($ch, $opt, $value) && \CURLOPT_CERTINFO !== $opt && (!\defined('CURLOPT_HEADEROPT') || \CURLOPT_HEADEROPT !== $opt)) {
                $constantName = $this->findConstantName($opt);
                throw new TransportException(\sprintf('Curl option "%s" is not supported.', $constantName ?? $opt));
            }
        }

        return $pushedResponse ?? new CurlResponse($this->multi, $ch, $options, $this->logger, $method, self::createRedirectResolver($options, $authority), CurlClientState::$curlVersion['version_number'], $url);
    }

    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface
    {
        if ($responses instanceof CurlResponse) {
            $responses = [$responses];
        }

        if ($this->multi->handle instanceof \CurlMultiHandle) {
            $active = 0;
            while (\CURLM_CALL_MULTI_PERFORM === curl_multi_exec($this->multi->handle, $active)) {
            }
        }

        return new ResponseStream(CurlResponse::stream($responses, $timeout));
    }

    public function reset(): void
    {
        $this->multi->reset();
    }

    /**
     * Accepts pushed responses only if their headers related to authentication match the request.
     */
    private static function acceptPushForRequest(string $method, array $options, PushedResponse $pushedResponse): bool
    {
        if ('' !== $options['body'] || $method !== $pushedResponse->requestHeaders[':method'][0]) {
            return false;
        }

        foreach (['proxy', 'no_proxy', 'bindto', 'local_cert', 'local_pk'] as $k) {
            if ($options[$k] !== $pushedResponse->parentOptions[$k]) {
                return false;
            }
        }

        foreach (['authorization', 'cookie', 'range', 'proxy-authorization'] as $k) {
            $normalizedHeaders = $options['normalized_headers'][$k] ?? [];
            foreach ($normalizedHeaders as $i => $v) {
                $normalizedHeaders[$i] = substr($v, \strlen($k) + 2);
            }

            if (($pushedResponse->requestHeaders[$k] ?? []) !== $normalizedHeaders) {
                return false;
            }
        }

        $statusCode = $pushedResponse->response->getInfo('http_code') ?: 200;

        return $statusCode < 300 || 400 <= $statusCode;
    }

    /**
     * Wraps the request's body callback to allow it to return strings longer than curl requested.
     */
    private static function readRequestBody(int $length, \Closure $body, string &$buffer, bool &$eof): string