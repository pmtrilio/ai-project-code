
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
     * @return void
     */
    public function setAttributes(array $attributes);

    public function hasAttribute(string $name): bool;

    /**
     * @throws \InvalidArgumentException When attribute doesn't exist for this token
     */
    public function getAttribute(string $name): mixed;

    /**
     * @return void
     */
    public function setAttribute(string $name, mixed $value);

    /**
     * Returns all the necessary state of the object for serialization purposes.
     */
    public function __serialize(): array;

    /**
     * Restores the object state from an array given by __serialize().
     */
    public function __unserialize(array $data): void;
}
