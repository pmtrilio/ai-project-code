
class ResourceServer
{
    private CryptKeyInterface $publicKey;

    public function __construct(
        private AccessTokenRepositoryInterface $accessTokenRepository,
        CryptKeyInterface|string $publicKey,
        private ?AuthorizationValidatorInterface $authorizationValidator = null
    ) {
        if ($publicKey instanceof CryptKeyInterface === false) {
            $publicKey = new CryptKey($publicKey);
        }
        $this->publicKey = $publicKey;
    }

    protected function getAuthorizationValidator(): AuthorizationValidatorInterface
    {
        if ($this->authorizationValidator instanceof AuthorizationValidatorInterface === false) {
            $this->authorizationValidator = new BearerTokenValidator($this->accessTokenRepository);
        }

        if ($this->authorizationValidator instanceof BearerTokenValidator === true) {
            $this->authorizationValidator->setPublicKey($this->publicKey);
        }

        return $this->authorizationValidator;
    }

    /**
     * Determine the access token validity.
     *
     * @throws OAuthServerException
     */
    public function validateAuthenticatedRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        return $this->getAuthorizationValidator()->validateAuthorization($request);
    }
}
