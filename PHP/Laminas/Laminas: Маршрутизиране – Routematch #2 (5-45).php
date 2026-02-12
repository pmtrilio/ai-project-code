namespace Laminas\Router;

use function array_key_exists;

/**
 * RouteInterface match.
 */
class RouteMatch
{
    /**
     * Match parameters.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Matched route name.
     *
     * @var string
     */
    protected $matchedRouteName;

    /**
     * Create a RouteMatch with given parameters.
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Set name of matched route.
     *
     * @param  string $name
     * @return RouteMatch
     */
    public function setMatchedRouteName($name)
    {
        $this->matchedRouteName = $name;
        return $this;