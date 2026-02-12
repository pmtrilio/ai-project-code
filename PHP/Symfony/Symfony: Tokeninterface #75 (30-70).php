
    /**
     * Returns the user identifier used during authentication (e.g. a user's email address or username).
     */
    public function getUserIdentifier(): string;

    /**
     * Returns the user roles.
     *
     * @return string[]
     */
    public function getRoleNames(): array;

    /**
     * Returns a user representation.
     *
     * @see AbstractToken::setUser()
     */
    public function getUser(): ?UserInterface;

    /**
     * Sets the authenticated user in the token.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function setUser(UserInterface $user);

    /**
     * Removes sensitive information from the token.
     *
     * @return void
     */
    public function eraseCredentials();

    public function getAttributes(): array;

    /**
     * @param array $attributes The token attributes
     *