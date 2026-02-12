     * The Translator implementation.
     *
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * The container instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The Presence Verifier implementation.
     *
     * @var \Illuminate\Validation\PresenceVerifierInterface
     */
    protected $presenceVerifier;

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * Attributes that should be excluded from the validated data.
     *
     * @var array
     */
    protected $excludeAttributes = [];

    /**
     * The message bag instance.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $messages;

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * The initial rules provided.
     *
     * @var array
     */
    protected $initialRules;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    protected $rules;

    /**
     * The current rule that is validating.
     *
     * @var string
     */
    protected $currentRule;

    /**
     * The array of wildcard attributes with their asterisks expanded.
     *
     * @var array
     */
    protected $implicitAttributes = [];

    /**
     * The callback that should be used to format the attribute.
     *
     * @var callable|null
     */