 */
final class Envelope
{
    /**
     * @var array<class-string<StampInterface>, list<StampInterface>>
     */
    private array $stamps = [];
    private object $message;

    /**
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
    public function withoutAll(string $stampFqcn): static
    {
        $cloned = clone $this;

        unset($cloned->stamps[$stampFqcn]);

        return $cloned;
    }

    /**
     * Removes all stamps that implement the given type.
     */
    public function withoutStampsOfType(string $type): self
    {
        $cloned = clone $this;

        foreach ($cloned->stamps as $class => $stamps) {
            if ($class === $type || is_subclass_of($class, $type)) {
                unset($cloned->stamps[$class]);
            }
        }

        return $cloned;
    }

    /**
     * @template TStamp of StampInterface
     *
     * @param class-string<TStamp> $stampFqcn
     *