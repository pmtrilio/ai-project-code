<?php

namespace Illuminate\Mail;

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
     */
    protected $transport;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The global from address and name.
     *
     * @var array
     */
    protected $from;

    /**
     * The global reply-to address and name.
     *
     * @var array
     */
    protected $replyTo;

    /**
     * The global return path address.
     *
     * @var array
     */
    protected $returnPath;

    /**
     * The global to address and name.
     *
     * @var array
     */
    protected $to;

    /**
     * The queue factory implementation.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $queue;

    /**
     * Create a new Mailer instance.
     *
     * @param  string  $name
     * @param  \Illuminate\Contracts\View\Factory  $views
     * @param  \Symfony\Component\Mailer\Transport\TransportInterface  $transport
     * @param  \Illuminate\Contracts\Events\Dispatcher|null  $events
     */
    public function __construct(string $name, Factory $views, TransportInterface $transport, ?Dispatcher $events = null)
    {
        $this->name = $name;