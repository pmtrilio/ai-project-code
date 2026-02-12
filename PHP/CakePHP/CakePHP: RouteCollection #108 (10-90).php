 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Routing;

use Cake\Routing\Exception\DuplicateNamedRouteException;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Route\Route;
use Closure;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Contains a collection of routes.
 *
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