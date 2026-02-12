<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
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