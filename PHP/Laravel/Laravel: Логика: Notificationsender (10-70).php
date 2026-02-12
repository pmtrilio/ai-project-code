use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Localizable;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\Exception\TransportException;
use Throwable;

class NotificationSender
{
    use Localizable;

    /**
     * The notification manager instance.
     *
     * @var \Illuminate\Notifications\ChannelManager
     */
    protected $manager;

    /**
     * The Bus dispatcher instance.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    protected $bus;

    /**
     * The event dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The locale to be used when sending notifications.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * Indicates whether a NotificationFailed event has been dispatched.
     *
     * @var bool
     */
    protected $failedEventWasDispatched = false;

    /**
     * Create a new notification sender instance.
     *
     * @param  \Illuminate\Notifications\ChannelManager  $manager
     * @param  \Illuminate\Contracts\Bus\Dispatcher  $bus
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  string|null  $locale
     */
    public function __construct($manager, $bus, $events, $locale = null)
    {
        $this->bus = $bus;
        $this->events = $events;
        $this->locale = $locale;