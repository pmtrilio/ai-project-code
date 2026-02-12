use Laminas\Router\SimpleRouteStack;
use Laminas\ServiceManager\Config;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\RequestInterface as Request;
use Laminas\Uri\Http as HttpUri;
use Traversable;

use function array_merge;
use function explode;
use function is_array;
use function is_string;
use function method_exists;
use function rtrim;
use function sprintf;
use function strlen;

/**
 * Tree search implementation.
 *
 * @template TRoute of RouteInterface
 * @template-extends SimpleRouteStack<TRoute>
 */
class TreeRouteStack extends SimpleRouteStack
{
    /**
     * Base URL.
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * Request URI.
     *
     * @var HttpUri
     */
    protected $requestUri;

    /**
     * Prototype routes.
     *
     * We use an ArrayObject in this case so we can easily pass it down the tree
     * by reference.
     *
     * @var ArrayObject<string, TRoute>
     */
    protected $prototypes;

    /**
     * @internal
     * @deprecated Since 3.9.0 This property will be removed or made private in version 4.0
     *
     * @var int|null
     */
    public $priority;

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::factory()
     *
     * @param  iterable $options
     * @return SimpleRouteStack
     * @throws Exception\InvalidArgumentException
     */
    public static function factory($options = [])
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (! is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable set of options',
                __METHOD__
            ));
        }

        $instance = parent::factory($options);

        if (isset($options['prototypes'])) {