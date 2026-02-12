<?php

declare(strict_types=1);

namespace Laminas\Stratigility;

use ArrayIterator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;
use Traversable;

use function iterator_to_array;

/**
 * Pipe middleware like unix pipes.
 *
 * This class implements a pipeline of middleware, which can be attached using
 * the `pipe()` method, and is itself middleware.
 *
 * It creates an instance of `Next` internally, invoking it with the provided
 * request and response instances, passing the original request and the returned
 * response to the `$next` argument when complete.
 *
 * Inspired by Sencha Connect.
 *
 * @see https://github.com/senchalabs/connect
 */
final class MiddlewarePipe implements IterableMiddlewarePipeInterface
{
    /** @var SplQueue<MiddlewareInterface> */
    private SplQueue $pipeline;

    /**
     * Initializes the queue.
     */
    public function __construct()
    {
        /** @psalm-var SplQueue<MiddlewareInterface> */