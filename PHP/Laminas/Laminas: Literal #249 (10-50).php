use Traversable;

use function is_array;
use function method_exists;
use function sprintf;
use function strlen;
use function strpos;

/**
 * Literal route.
 */
class Literal implements RouteInterface
{
    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * @internal
     * @deprecated Since 3.9.0 This property will be removed or made private in version 4.0
     *
     * @var int|null
     */
    public $priority;

    /**
     * Create a new literal route.
     *
     * @param  string $route
     */
    public function __construct(
        /**
         * RouteInterface to match.
         */
        protected $route,
        array $defaults = []
    ) {
        $this->defaults = $defaults;