 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface TokenInterface extends \Stringable
{
    /**
     * Returns a string representation of the Token.
     *
     * This is only to be used for debugging purposes.
     */
    public function __toString(): string;

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