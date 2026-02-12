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