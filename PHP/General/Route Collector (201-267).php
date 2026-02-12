                return $route;
            }

            unset($this->routesByName[$name]);
        }

        foreach ($this->routes as $route) {
            if ($name === $route->getName()) {
                $this->routesByName[$name] = $route;
                return $route;
            }
        }

        throw new RuntimeException('Named route does not exist for name: ' . $name);
    }

    /**
     * {@inheritdoc}
     */
    public function lookupRoute(string $identifier): RouteInterface
    {
        if (!isset($this->routes[$identifier])) {
            throw new RuntimeException('Route not found, looks like your route cache is stale.');
        }
        return $this->routes[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function group(string $pattern, $callable): RouteGroupInterface
    {
        $routeGroup = $this->createGroup($pattern, $callable);
        $this->routeGroups[] = $routeGroup;

        $routeGroup->collectRoutes();
        array_pop($this->routeGroups);

        return $routeGroup;
    }

    /**
     * @param string|callable $callable
     */
    protected function createGroup(string $pattern, $callable): RouteGroupInterface
    {
        $routeCollectorProxy = $this->createProxy($pattern);
        return new RouteGroup($pattern, $callable, $this->callableResolver, $routeCollectorProxy);
    }

    /**
     * @return RouteCollectorProxyInterface<TContainerInterface>
     */
    protected function createProxy(string $pattern): RouteCollectorProxyInterface
    {
        /** @var RouteCollectorProxy<TContainerInterface> */
        return new RouteCollectorProxy(
            $this->responseFactory,
            $this->callableResolver,
            $this->container,
            $this,
            $pattern
        );
    }

    /**
     * {@inheritdoc}