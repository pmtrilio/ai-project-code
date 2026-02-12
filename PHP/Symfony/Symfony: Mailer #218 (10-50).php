 */

namespace Symfony\Component\Mailer;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\RawMessage;

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class Mailer implements MailerInterface
{
    private TransportInterface $transport;
    private ?MessageBusInterface $bus;
    private ?EventDispatcherInterface $dispatcher;

    public function __construct(TransportInterface $transport, ?MessageBusInterface $bus = null, ?EventDispatcherInterface $dispatcher = null)
    {
        $this->transport = $transport;
        $this->bus = $bus;
        $this->dispatcher = $dispatcher;
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): void
    {
        if (null === $this->bus) {
            $this->transport->send($message, $envelope);

            return;
        }

        $stamps = [];
        if (null !== $this->dispatcher) {
            // The dispatched event here has `queued` set to `true`; the goal is NOT to render the message, but to let
            // listeners do something before a message is sent to the queue.