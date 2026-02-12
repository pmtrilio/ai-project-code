use SensitiveParameter;

class AuthorizationServer implements EmitterAwareInterface
{
    use EmitterAwarePolyfill;

    /**
     * @var GrantTypeInterface[]
     */
    protected array $enabledGrantTypes = [];

    /**
     * @var DateInterval[]
     */
    protected array $grantTypeAccessTokenTTL = [];

    protected CryptKeyInterface $privateKey;

    protected CryptKeyInterface $publicKey;

    protected ResponseTypeInterface $responseType;

    private string|Key $encryptionKey;

    private string $defaultScope = '';

    private bool $revokeRefreshTokens = true;

    /**
     * New server instance
     */
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private AccessTokenRepositoryInterface $accessTokenRepository,
        private ScopeRepositoryInterface $scopeRepository,
        #[SensitiveParameter]
        CryptKeyInterface|string $privateKey,
        #[SensitiveParameter]
        Key|string $encryptionKey,
        ResponseTypeInterface|null $responseType = null
    ) {
        if ($privateKey instanceof CryptKeyInterface === false) {
            $privateKey = new CryptKey($privateKey);
        }

        $this->privateKey = $privateKey;
        $this->encryptionKey = $encryptionKey;

        if ($responseType === null) {
            $responseType = new BearerTokenResponse();
        } else {
            $responseType = clone $responseType;
        }

        $this->responseType = $responseType;
    }

    /**
     * Enable a grant type on the server
     */
    public function enableGrantType(GrantTypeInterface $grantType, DateInterval|null $accessTokenTTL = null): void
    {
        if ($accessTokenTTL === null) {
            $accessTokenTTL = new DateInterval('PT1H');
        }

        $grantType->setAccessTokenRepository($this->accessTokenRepository);
        $grantType->setClientRepository($this->clientRepository);
        $grantType->setScopeRepository($this->scopeRepository);
        $grantType->setDefaultScope($this->defaultScope);
        $grantType->setPrivateKey($this->privateKey);
        $grantType->setEmitter($this->getEmitter());
        $grantType->setEncryptionKey($this->encryptionKey);
        $grantType->revokeRefreshTokens($this->revokeRefreshTokens);

        $this->enabledGrantTypes[$grantType->getIdentifier()] = $grantType;
        $this->grantTypeAccessTokenTTL[$grantType->getIdentifier()] = $accessTokenTTL;
    }

    /**
     * Validate an authorization request
     *
     * @throws OAuthServerException
     */
    public function validateAuthorizationRequest(ServerRequestInterface $request): AuthorizationRequestInterface
    {
        foreach ($this->enabledGrantTypes as $grantType) {
            if ($grantType->canRespondToAuthorizationRequest($request)) {
                return $grantType->validateAuthorizationRequest($request);
            }
        }

        throw OAuthServerException::unsupportedGrantType();
    }

    /**
     * Complete an authorization request
     */
    public function completeAuthorizationRequest(
        AuthorizationRequestInterface $authRequest,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->enabledGrantTypes[$authRequest->getGrantTypeId()]
            ->completeAuthorizationRequest($authRequest)
            ->generateHttpResponse($response);
    }

    /**
     * Respond to device authorization request
     *
     * @throws OAuthServerException
     */
    public function respondToDeviceAuthorizationRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        foreach ($this->enabledGrantTypes as $grantType) {
            if ($grantType->canRespondToDeviceAuthorizationRequest($request)) {
                return $grantType
                    ->respondToDeviceAuthorizationRequest($request)
                    ->generateHttpResponse($response);
            }
        }