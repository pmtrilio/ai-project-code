     * @param object|Envelope  $message
     * @param StampInterface[] $stamps
     */
    public function __construct(object $message, array $stamps = [])
    {
        $this->message = $message;

        foreach ($stamps as $stamp) {
            $this->stamps[$stamp::class][] = $stamp;
        }
    }

    /**
     * Makes sure the message is in an Envelope and adds the given stamps.
     *
     * @param StampInterface[] $stamps
     */
    public static function wrap(object $message, array $stamps = []): self
    {
        $envelope = $message instanceof self ? $message : new self($message);

        return $envelope->with(...$stamps);
    }

    /**
     * Adds one or more stamps.
     */
    public function with(StampInterface ...$stamps): static
    {
        $cloned = clone $this;

        foreach ($stamps as $stamp) {
            $cloned->stamps[$stamp::class][] = $stamp;
        }

        return $cloned;
    }

    /**
     * Removes all stamps of the given class.
     */