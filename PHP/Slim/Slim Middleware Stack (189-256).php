                }

                if (!$callable) {
                    $resolved = $this->middleware;
                    $instance = null;
                    $method = null;

                    /** @psalm-suppress ArgumentTypeCoercion */
                    // Check for Slim callable as `class:method`
                    if (preg_match(CallableResolver::$callablePattern, $resolved, $matches)) {
                        $resolved = $matches[1];
                        $method = $matches[2];
                    }

                    if ($this->container && $this->container->has($resolved)) {
                        $instance = $this->container->get($resolved);
                        if ($instance instanceof MiddlewareInterface) {
                            return $instance->process($request, $this->next);
                        }
                    } elseif (!function_exists($resolved)) {
                        if (!class_exists($resolved)) {
                            throw new RuntimeException(sprintf('Middleware %s does not exist', $resolved));
                        }
                        $instance = new $resolved($this->container);
                    }

                    if ($instance && $instance instanceof MiddlewareInterface) {
                        return $instance->process($request, $this->next);
                    }

                    $callable = $instance ?? $resolved;
                    if ($instance && $method) {
                        $callable = [$instance, $method];
                    }

                    if ($this->container && $callable instanceof Closure) {
                        $callable = $callable->bindTo($this->container);
                    }
                }

                if (!is_callable($callable)) {
                    throw new RuntimeException(
                        sprintf(
                            'Middleware %s is not resolvable',
                            $this->middleware
                        )
                    );
                }

                /** @var ResponseInterface */
                return $callable($request, $this->next);
            }
        };

        return $this;
    }

    /**
     * Add a (non-standard) callable middleware to the stack
     *
     * Middleware are organized as a stack. That means middleware
     * that have been added before will be executed after the newly
     * added one (last in, first out).
     * @return MiddlewareDispatcher<TContainerInterface>
     */
    public function addCallable(callable $middleware): self
    {
        $next = $this->tip;