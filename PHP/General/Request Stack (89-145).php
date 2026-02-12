     * Returns the parent request of the current.
     *
     * Be warned that making your code aware of the parent request
     * might make it un-compatible with other features of your framework
     * like ESI support.
     *
     * If current Request is the main request, it returns null.
     */
    public function getParentRequest(): ?Request
    {
        $pos = \count($this->requests) - 2;

        return $this->requests[$pos] ?? null;
    }

    /**
     * Gets the current session.
     *
     * @throws SessionNotFoundException
     */
    public function getSession(): SessionInterface
    {
        if ((null !== $request = end($this->requests) ?: null) && $request->hasSession()) {
            return $request->getSession();
        }

        throw new SessionNotFoundException();
    }

    public function resetRequestFormats(): void
    {
        static $resetRequestFormats;
        $resetRequestFormats ??= \Closure::bind(static fn () => self::$formats = null, null, Request::class);
        $resetRequestFormats();
    }
}
