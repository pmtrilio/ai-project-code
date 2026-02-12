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