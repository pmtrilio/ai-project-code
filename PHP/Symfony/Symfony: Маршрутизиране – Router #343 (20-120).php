use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\CompiledUrlGenerator;
use Symfony\Component\Routing\Generator\ConfigurableRequirementsInterface;
use Symfony\Component\Routing\Generator\Dumper\CompiledUrlGeneratorDumper;
use Symfony\Component\Routing\Generator\Dumper\GeneratorDumperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Matcher\Dumper\MatcherDumperInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * The Router class is an example of the integration of all pieces of the
 * routing system for easier use.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Router implements RouterInterface, RequestMatcherInterface
{
    /**
     * @var UrlMatcherInterface|null
     */
    protected $matcher;

    /**
     * @var UrlGeneratorInterface|null
     */
    protected $generator;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var RouteCollection|null
     */
    protected $collection;

    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var string|null
     */
    protected $defaultLocale;

    private ConfigCacheFactoryInterface $configCacheFactory;

    /**
     * @var ExpressionFunctionProviderInterface[]
     */
    private array $expressionLanguageProviders = [];

    private static ?array $cache = [];

    public function __construct(LoaderInterface $loader, mixed $resource, array $options = [], ?RequestContext $context = null, ?LoggerInterface $logger = null, ?string $defaultLocale = null)
    {
        $this->loader = $loader;
        $this->resource = $resource;
        $this->logger = $logger;
        $this->context = $context ?? new RequestContext();
        $this->setOptions($options);
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * cache_dir:              The cache directory (or null to disable caching)
     *   * debug:                  Whether to enable debugging or not (false by default)
     *   * generator_class:        The name of a UrlGeneratorInterface implementation
     *   * generator_dumper_class: The name of a GeneratorDumperInterface implementation
     *   * matcher_class:          The name of a UrlMatcherInterface implementation
     *   * matcher_dumper_class:   The name of a MatcherDumperInterface implementation
     *   * resource_type:          Type hint for the main resource (optional)
     *   * strict_requirements:    Configure strict requirement checking for generators
     *                             implementing ConfigurableRequirementsInterface (default is true)
     *
     * @return void
     *