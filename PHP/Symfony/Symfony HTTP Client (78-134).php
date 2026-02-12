        // string - the interface or the local socket to bind to
        'bindto' => '0',
        // see https://php.net/context.ssl for the following options
        'verify_peer' => true,
        'verify_host' => true,
        'cafile' => null,
        'capath' => null,
        'local_cert' => null,
        'local_pk' => null,
        'passphrase' => null,
        'ciphers' => null,
        'peer_fingerprint' => null,
        'capture_peer_cert_chain' => false,
        // STREAM_CRYPTO_METHOD_TLSv*_CLIENT - minimum TLS version
        'crypto_method' => \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
        // array - additional options that can be ignored if unsupported, unlike regular options
        'extra' => [
            // bool - whether to use persistent connections where supported
            'use_persistent_connections' => false,
        ],
    ];

    /**
     * Requests an HTTP resource.
     *
     * Responses MUST be lazy, but their status code MUST be
     * checked even if none of their public methods are called.
     *
     * Implementations are not required to support all options described above; they can also
     * support more custom options; but in any case, they MUST throw a TransportExceptionInterface
     * when an unsupported option is passed.
     *
     * @throws TransportExceptionInterface When an unsupported option is passed
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface;

    /**
     * Yields responses chunk by chunk as they complete.
     *
     * @param ResponseInterface|iterable<array-key, ResponseInterface> $responses One or more responses created by the current HTTP client
     * @param float|null                                               $timeout   The idle timeout before yielding timeout chunks
     */
    public function stream(ResponseInterface|iterable $responses, ?float $timeout = null): ResponseStreamInterface;

    /**
     * Returns a new instance of the client with new default options.
     */
    public function withOptions(array $options): static;
}
