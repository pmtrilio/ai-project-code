 * Provides an interface for adding/removing routes
 * and parsing/generating URLs with the routes it contains.
 *
 * @internal
 */
class RouteCollection
{
    /**
     * The routes connected to this collection.
     *
     * @var array<string, array<\Cake\Routing\Route\Route>>
     */
    protected array $_routeTable = [];

    /**
     * The hash map of named routes that are in this collection.
     *
     * @var array<\Cake\Routing\Route\Route>
     */
    protected array $_named = [];

    /**
     * Routes indexed by static path.
     *
     * @var array<string, array<\Cake\Routing\Route\Route>>
     */
    protected array $staticPaths = [];

    /**
     * Routes indexed by path prefix.
     *
     * @var array<string, array<\Cake\Routing\Route\Route>>
     */
    protected array $_paths = [];

    /**
     * A map of middleware names and the related objects.
     *
     * @var array
     */
    protected array $_middleware = [];

    /**
     * A map of middleware group names and the related middleware names.
     *
     * @var array
     */
    protected array $_middlewareGroups = [];

    /**
     * Route extensions
     *
     * @var array<string>
     */
    protected array $_extensions = [];

    /**
     * Add a route to the collection.
     *
     * @param \Cake\Routing\Route\Route $route The route object to add.
     * @param array<string, mixed> $options Additional options for the route. Primarily for the
     *   `_name` option, which enables named routes.
     * @return void
     */
    public function add(Route $route, array $options = []): void
    {
        // Explicit names
        if (isset($options['_name'])) {
            if (isset($this->_named[$options['_name']])) {
                $matched = $this->_named[$options['_name']];
                throw new DuplicateNamedRouteException([
                    'name' => $options['_name'],
                    'url' => $matched->template,
                    'duplicate' => $matched,
                ]);
            }
            $this->_named[$options['_name']] = $route;
        }

        // Generated names.
        $name = $route->getName();