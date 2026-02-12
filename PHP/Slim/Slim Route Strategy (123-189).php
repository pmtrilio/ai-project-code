     * This method traverses the middleware stack, including the route's callable
     * and captures the resultant HTTP response object. It then sends the response
     * back to the Application.
     */
    public function run(ServerRequestInterface $request): ResponseInterface;
}
