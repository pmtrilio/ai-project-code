<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\Firewall\FirewallListenerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Firewall uses a FirewallMap to register security listeners for the given
 * request.
 *
 * It allows for different security strategies within the same application
 * (a Basic authentication for the /api, and a web based authentication for
 * everything else for instance).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Firewall implements EventSubscriberInterface
{
    private FirewallMapInterface $map;
    private EventDispatcherInterface $dispatcher;

    /**
     * @var \SplObjectStorage<Request, ExceptionListener>
     */
    private \SplObjectStorage $exceptionListeners;

    public function __construct(FirewallMapInterface $map, EventDispatcherInterface $dispatcher)
    {
        $this->map = $map;
        $this->dispatcher = $dispatcher;
        $this->exceptionListeners = new \SplObjectStorage();
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // register listeners for this firewall
        $listeners = $this->map->getListeners($event->getRequest());
