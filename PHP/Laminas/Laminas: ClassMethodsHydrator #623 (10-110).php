use Webmozart\Assert\Assert;

use function get_class_methods;
use function is_callable;
use function lcfirst;
use function method_exists;
use function property_exists;
use function spl_object_hash;
use function sprintf;
use function str_contains;
use function str_starts_with;
use function substr;
use function ucfirst;

/**
 * @final
 */
class ClassMethodsHydrator extends AbstractHydrator implements HydratorOptionsInterface
{
    /**
     * Flag defining whether array keys are underscore-separated (true) or camel case (false)
     *
     * @var bool
     */
    protected $underscoreSeparatedKeys = true;

    /**
     * Flag defining whether to check the setter method with method_exists to prevent the
     * hydrator from calling __call during hydration
     *
     * @var bool
     */
    protected $methodExistsCheck = false;

    /**
     * Holds the names of the methods used for hydration, indexed by class::property name,
     * false if the hydration method is not callable/usable for hydration purposes
     *
     * @var string[]|bool[]
     */
    private $hydrationMethodsCache = [];

    /**
     * A map of extraction methods to property name to be used during extraction, indexed
     * by class name and method name
     *
     * @var null[]|string[][]
     */
    private $extractionMethodsCache = [];

    private FilterInterface $optionalParametersFilter;

    /**
     * Define if extract values will use camel case or name with underscore
     */
    public function __construct(bool $underscoreSeparatedKeys = true, bool $methodExistsCheck = false)
    {
        $this->setUnderscoreSeparatedKeys($underscoreSeparatedKeys);
        $this->setMethodExistsCheck($methodExistsCheck);

        $this->optionalParametersFilter = new Filter\OptionalParametersFilter();

        $compositeFilter = $this->getCompositeFilter();
        $compositeFilter->addFilter('is', new Filter\IsFilter());
        $compositeFilter->addFilter('has', new Filter\HasFilter());
        $compositeFilter->addFilter('get', new Filter\GetFilter());
    }

    /**
     * @param mixed[] $options
     */
    public function setOptions(iterable $options): void
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['underscoreSeparatedKeys'])) {
            $this->setUnderscoreSeparatedKeys($options['underscoreSeparatedKeys']);
        }

        if (isset($options['methodExistsCheck'])) {
            $this->setMethodExistsCheck($options['methodExistsCheck']);
        }
    }

    public function setUnderscoreSeparatedKeys(bool $underscoreSeparatedKeys): void
    {
        $this->underscoreSeparatedKeys = $underscoreSeparatedKeys;

        if ($this->underscoreSeparatedKeys) {
            $this->setNamingStrategy(new NamingStrategy\UnderscoreNamingStrategy());
            return;
        }

        if ($this->hasNamingStrategy()) {
            $this->removeNamingStrategy();
            return;
        }
    }
