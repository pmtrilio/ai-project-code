use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface;
use League\OAuth2\Server\ResponseTypes\AbstractResponseType;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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