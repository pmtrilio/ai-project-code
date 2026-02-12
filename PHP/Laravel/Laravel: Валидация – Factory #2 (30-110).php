     * @var \Illuminate\Contracts\Container\Container|null
     */
    protected $container;

    /**
     * All of the custom validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $extensions = [];

    /**
     * All of the custom implicit validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $implicitExtensions = [];

    /**
     * All of the custom dependent validator extensions.
     *
     * @var array<string, \Closure|string>
     */
    protected $dependentExtensions = [];

    /**
     * All of the custom validator message replacers.
     *
     * @var array<string, \Closure|string>
     */
    protected $replacers = [];

    /**
     * All of the fallback messages for custom rules.
     *
     * @var array<string, string>
     */
    protected $fallbackMessages = [];

    /**
     * Indicates that unvalidated array keys should be excluded, even if the parent array was validated.
     *
     * @var bool
     */
    protected $excludeUnvalidatedArrayKeys = true;

    /**
     * The Validator resolver instance.
     *
     * @var \Closure
     */
    protected $resolver;

    /**
     * Create a new Validator factory instance.
     *
     * @param  \Illuminate\Contracts\Translation\Translator  $translator
     * @param  \Illuminate\Contracts\Container\Container|null  $container
     */
    public function __construct(Translator $translator, ?Container $container = null)
    {
        $this->container = $container;
        $this->translator = $translator;
    }

    /**
     * Create a new Validator instance.
     *
     * @param  array  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return \Illuminate\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $attributes = [])
    {
        $validator = $this->resolve(
            $data, $rules, $messages, $attributes
        );

        // The presence verifier is responsible for checking the unique and exists data