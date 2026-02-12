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