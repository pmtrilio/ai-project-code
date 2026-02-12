     * The loader implementation.
     *
     * @var \Illuminate\Contracts\Translation\Loader
     */
    protected $loader;

    /**
     * The default locale being used by the translator.
     *
     * @var string
     */
    protected $locale;

    /**
     * The fallback locale used by the translator.
     *
     * @var string
     */
    protected $fallback;

    /**
     * The array of loaded translation groups.
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * The message selector.
     *
     * @var \Illuminate\Translation\MessageSelector
     */
    protected $selector;

    /**
     * The callable that should be invoked to determine applicable locales.
     *
     * @var callable
     */
    protected $determineLocalesUsing;

    /**
     * The custom rendering callbacks for stringable objects.
     *
     * @var array
     */
    protected $stringableHandlers = [];

    /**
     * The callback that is responsible for handling missing translation keys.
     *
     * @var callable|null
     */
    protected $missingTranslationKeyCallback;

    /**
     * Indicates whether missing translation keys should be handled.
     *
     * @var bool
     */
    protected $handleMissingTranslationKeys = true;

    /**
     * Create a new translator instance.
     *
     * @param  \Illuminate\Contracts\Translation\Loader  $loader
     * @param  string  $locale
     */
    public function __construct(Loader $loader, $locale)
    {
        $this->loader = $loader;

        $this->setLocale($locale);
    }

    /**
     * Determine if a translation exists for a given locale.
     *
     * @param  string  $key
     * @param  string|null  $locale
     * @return bool