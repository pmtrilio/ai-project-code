     * @param string $name Route name
     *
     * @throws RuntimeException   If named route does not exist
     */
    public function getNamedRoute(string $name): RouteInterface;

    /**
     * Remove named route
     *
     * @param string $name Route name
     *
     * @throws RuntimeException   If named route does not exist
     */
    public function removeNamedRoute(string $name): RouteCollectorInterface;

    /**
     * Lookup a route via the route's unique identifier
     *
     * @throws RuntimeException   If route of identifier does not exist
     */
    public function lookupRoute(string $identifier): RouteInterface;

    /**
     * Add route group
     * @param string|callable $callable
     */
    public function group(string $pattern, $callable): RouteGroupInterface;

    /**
     * Add route
     *
     * @param string[] $methods Array of HTTP methods
     * @param string $pattern The route pattern
     * @param callable|array{class-string, string}|string $handler The route callable
     */
    public function map(array $methods, string $pattern, $handler): RouteInterface;
}
