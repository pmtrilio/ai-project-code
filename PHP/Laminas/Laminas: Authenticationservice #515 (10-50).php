
class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Persistent storage handler
     *
     * @var Storage\StorageInterface|null
     */
    protected $storage;

    /**
     * Authentication adapter
     *
     * @var Adapter\AdapterInterface|null
     */
    protected $adapter;

    /**
     * Constructor
     */
    public function __construct(?Storage\StorageInterface $storage = null, ?Adapter\AdapterInterface $adapter = null)
    {
        if (null !== $storage) {
            $this->setStorage($storage);
        }
        if (null !== $adapter) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Returns the authentication adapter
     *
     * The adapter does not have a default if the storage adapter has not been set.
     *
     * @return Adapter\AdapterInterface|null
     */
    public function getAdapter()
    {
        return $this->adapter;
    }