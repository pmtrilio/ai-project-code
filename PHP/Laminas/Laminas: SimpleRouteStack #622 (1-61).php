<?php

declare(strict_types=1);

namespace Laminas\Router;

use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\RequestInterface as Request;
use Traversable;

use function array_merge;
use function is_array;
use function sprintf;

/**
 * Simple route stack implementation.
 *
 * @template TRoute of RouteInterface
 * @template-implements RouteStackInterface<TRoute>
 */
class SimpleRouteStack implements RouteStackInterface
{
    /**
     * Stack containing all routes.
     *
     * @var PriorityList<string, TRoute>
     */
    protected $routes;

    /**
     * Route plugin manager
     *
     * @var RoutePluginManager<TRoute>
     */
    protected $routePluginManager;

    /**
     * Default parameters.
     *
     * @var array
     */
    protected $defaultParams = [];

    /**
     * @param RoutePluginManager<TRoute>|null $routePluginManager
     */
    public function __construct(?RoutePluginManager $routePluginManager = null)
    {
        /** @var PriorityList<string, TRoute> $this->routes */
        $this->routes = new PriorityList();
        /** @var RoutePluginManager<TRoute> $this->routePluginManager */
        $this->routePluginManager = $routePluginManager ?? new RoutePluginManager(new ServiceManager());

        $this->init();
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    \Laminas\Router\RouteInterface::factory()