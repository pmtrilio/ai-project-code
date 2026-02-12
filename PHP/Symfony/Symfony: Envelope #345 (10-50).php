 */

namespace Symfony\Component\Messenger;

use Symfony\Component\Messenger\Stamp\StampInterface;

/**
 * A message wrapped in an envelope with stamps (configurations, markers, ...).
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
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
