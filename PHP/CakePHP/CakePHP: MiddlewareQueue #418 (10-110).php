 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Http;

use Cake\Core\App;
use Cake\Core\ContainerInterface;
use Cake\Http\Middleware\ClosureDecoratorMiddleware;
use Closure;
use Countable;
use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;
use Psr\Http\Server\MiddlewareInterface;
use SeekableIterator;

/**
 * Provides methods for creating and manipulating a "queue" of middlewares.
 * This queue is used to process a request and generate response via \Cake\Http\Runner.
 *
 * @template-implements \SeekableIterator<int, \Psr\Http\Server\MiddlewareInterface>
 */
class MiddlewareQueue implements Countable, SeekableIterator
{
    /**
     * Internal position for iterator.
     *
     * @var int
     */
    protected int $position = 0;

    /**
     * The queue of middlewares.
     *
     * @var array<int, mixed>
     */
    protected array $queue = [];

    /**
     * @var \Cake\Core\ContainerInterface|null
     */
    protected ?ContainerInterface $container;

    /**
     * Constructor
     *
     * @param array $middleware The list of middleware to append.
     * @param \Cake\Core\ContainerInterface|null $container Container instance.
     */
    public function __construct(array $middleware = [], ?ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->queue = $middleware;
    }

    /**
     * Resolve middleware name to a PSR 15 compliant middleware instance.
     *
     * @param \Psr\Http\Server\MiddlewareInterface|\Closure|string $middleware The middleware to resolve.
     * @return \Psr\Http\Server\MiddlewareInterface
     * @throws \InvalidArgumentException If Middleware not found.
     */
    protected function resolve(MiddlewareInterface|Closure|string $middleware): MiddlewareInterface
    {
        if (is_string($middleware)) {
            if ($this->container && $this->container->has($middleware)) {
                $middleware = $this->container->get($middleware);
            } else {
                /** @var class-string<\Psr\Http\Server\MiddlewareInterface>|null $className */
                $className = App::className($middleware, 'Middleware', 'Middleware');
                if ($className === null) {
                    throw new InvalidArgumentException(sprintf(
                        'Middleware `%s` was not found.',
                        $middleware,
                    ));
                }
                $middleware = new $className();
            }
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        return new ClosureDecoratorMiddleware($middleware);
    }

    /**
     * Append a middleware to the end of the queue.
     *
     * @param \Psr\Http\Server\MiddlewareInterface|\Closure|array|string $middleware The middleware(s) to append.
     * @return $this
     */
    public function add(MiddlewareInterface|Closure|array|string $middleware)
    {
        if (is_array($middleware)) {
            $this->queue = array_merge($this->queue, $middleware);