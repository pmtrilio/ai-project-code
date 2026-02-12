class_exists(RewindableGenerator::class);
class_exists(ArgumentServiceLocator::class);

/**
 * Container is a dependency injection container.
 *
 * It gives access to object instances (services).
 * Services and parameters are simple key/pair stores.
 * The container can have four possible behaviors when a service
 * does not exist (or is not initialized for the last case):
 *
 *  * EXCEPTION_ON_INVALID_REFERENCE: Throws an exception at compilation time (the default)
 *  * NULL_ON_INVALID_REFERENCE:      Returns null
 *  * IGNORE_ON_INVALID_REFERENCE:    Ignores the wrapping command asking for the reference
 *                                    (for instance, ignore a setter if the service does not exist)
 *  * IGNORE_ON_UNINITIALIZED_REFERENCE: Ignores/returns null for uninitialized services or invalid references
 *  * RUNTIME_EXCEPTION_ON_INVALID_REFERENCE: Throws an exception at runtime
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Container implements ContainerInterface, ResetInterface
{
    protected $parameterBag;
    protected $services = [];
    protected $privates = [];
    protected $fileMap = [];
    protected $methodMap = [];
    protected $factories = [];
    protected $aliases = [];
    protected $loading = [];
    protected $resolving = [];
    protected $syntheticIds = [];

    private array $envCache = [];
    private bool $compiled = false;
    private \Closure $getEnv;

    private static \Closure $make;

    public function __construct(?ParameterBagInterface $parameterBag = null)
    {
        $this->parameterBag = $parameterBag ?? new EnvPlaceholderParameterBag();
    }

    /**
     * Compiles the container.
     *
     * This method does two things:
     *
     *  * Parameter values are resolved;
     *  * The parameter bag is frozen.
     *
     * @return void
     */
    public function compile()
    {
        $this->parameterBag->resolve();

        $this->parameterBag = new FrozenParameterBag(
            $this->parameterBag->all(),