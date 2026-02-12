use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Contracts\Mail\Mailer as MailerContract;
use Illuminate\Contracts\Mail\MailQueue as MailQueueContract;
use Illuminate\Contracts\Queue\Factory as QueueContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;

class Mailer implements MailerContract, MailQueueContract
{
    use Macroable;

    /**
     * The name that is configured for the mailer.
     *
     * @var string
     */
    protected $name;

    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $views;

    /**
     * The Symfony Transport instance.
     *
     * @var \Symfony\Component\Mailer\Transport\TransportInterface