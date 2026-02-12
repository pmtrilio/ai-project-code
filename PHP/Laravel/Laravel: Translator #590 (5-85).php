use Closure;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Contracts\Translation\Translator as TranslatorContract;
use Illuminate\Support\Arr;
use Illuminate\Support\NamespacedItemResolver;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\ReflectsClosures;
use InvalidArgumentException;

class Translator extends NamespacedItemResolver implements TranslatorContract
{
    use Macroable, ReflectsClosures;

    /**
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