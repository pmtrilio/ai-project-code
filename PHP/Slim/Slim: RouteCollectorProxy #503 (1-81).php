<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Routing;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;

/**
 * @template TContainerInterface of (ContainerInterface|null)
 * @template-implements RouteCollectorProxyInterface<TContainerInterface>
 */
class RouteCollectorProxy implements RouteCollectorProxyInterface
{
    protected ResponseFactoryInterface $responseFactory;

    protected CallableResolverInterface $callableResolver;

    /** @var TContainerInterface */
    protected ?ContainerInterface $container = null;

    protected RouteCollectorInterface $routeCollector;

    protected string $groupPattern;

    /**
     * @param TContainerInterface $container
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        CallableResolverInterface $callableResolver,
        ?ContainerInterface $container = null,
        ?RouteCollectorInterface $routeCollector = null,
        string $groupPattern = ''
    ) {
        $this->responseFactory = $responseFactory;
        $this->callableResolver = $callableResolver;
        $this->container = $container;
        $this->routeCollector = $routeCollector ?? new RouteCollector($responseFactory, $callableResolver, $container);
        $this->groupPattern = $groupPattern;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseFactory(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallableResolver(): CallableResolverInterface
    {
        return $this->callableResolver;
    }

    /**
     * {@inheritdoc}
     * @return TContainerInterface
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}