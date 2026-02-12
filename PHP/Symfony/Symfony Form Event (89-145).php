     *
     * This event can be used to modify the form depending on the final state of the underlying data
     * accessible in every representation: model, normalized and view.
     *
     * @Event("Symfony\Component\Form\Event\PostSetDataEvent")
     */
    public const POST_SET_DATA = 'form.post_set_data';

    /**
     * Event aliases.
     *
     * These aliases can be consumed by RegisterListenersPass.
     */
    public const ALIASES = [
        PreSubmitEvent::class => self::PRE_SUBMIT,
        SubmitEvent::class => self::SUBMIT,
        PostSubmitEvent::class => self::POST_SUBMIT,
        PreSetDataEvent::class => self::PRE_SET_DATA,
        PostSetDataEvent::class => self::POST_SET_DATA,
    ];

    private function __construct()
    {
    }
}
