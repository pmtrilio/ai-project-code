<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Messenger;

use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackMiddleware;

/**
 * @author Samuel Roze <samuel.roze@gmail.com>
 * @author Matthias Noback <matthiasnoback@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class MessageBus implements MessageBusInterface
{
    private \IteratorAggregate $middlewareAggregate;

    /**
     * @param iterable<mixed, MiddlewareInterface> $middlewareHandlers
     */
    public function __construct(iterable $middlewareHandlers = [])
    {
        if ($middlewareHandlers instanceof \IteratorAggregate) {
            $this->middlewareAggregate = $middlewareHandlers;
        } elseif (\is_array($middlewareHandlers)) {
            $this->middlewareAggregate = new \ArrayObject($middlewareHandlers);
        } else {
            // $this->middlewareAggregate should be an instance of IteratorAggregate.
            // When $middlewareHandlers is an Iterator, we wrap it to ensure it is lazy-loaded and can be rewound.
            $this->middlewareAggregate = new class($middlewareHandlers) implements \IteratorAggregate {
                private \Traversable $middlewareHandlers;
                private \ArrayObject $cachedIterator;
