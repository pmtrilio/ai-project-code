     *
     * @var \Illuminate\Notifications\NotificationSender|null
     */
    protected $notificationSender;

    /**
     * The default channel used to deliver messages.
     *
     * @var string
     */
    protected $defaultChannel = 'mail';

    /**
     * The locale used when sending notifications.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * Send the given notification to the given notifiable entities.
     *
     * @param  \Illuminate\Support\Collection|mixed  $notifiables
     * @param  mixed  $notification
     * @return void
     */
    public function send($notifiables, $notification)
    {
        $this->resolveNotificationSender()->send($notifiables, $notification);
    }

    /**
     * Send the given notification immediately.
     *
     * @param  \Illuminate\Support\Collection|mixed  $notifiables
     * @param  mixed  $notification
     * @param  array|null  $channels
     * @return void
     */
    public function sendNow($notifiables, $notification, ?array $channels = null)
    {