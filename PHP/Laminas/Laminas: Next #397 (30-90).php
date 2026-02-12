    {
        $this->queue = clone $queue;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue === null) {
            throw MiddlewarePipeNextHandlerAlreadyCalledException::create();
        }

        if ($this->queue->isEmpty()) {
            $this->queue = null;
            return $this->fallbackHandler->handle($request);
        }

        $middleware  = $this->queue->dequeue();
        $next        = clone $this; // deep clone is not used intentionally
        $this->queue = null; // mark queue as processed at this nesting level

        return $middleware->process($request, $next);
    }
}
