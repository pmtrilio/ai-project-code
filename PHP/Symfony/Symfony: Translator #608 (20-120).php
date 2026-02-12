use Symfony\Component\Translation\Formatter\IntlFormatterInterface;
use Symfony\Component\Translation\Formatter\MessageFormatter;
use Symfony\Component\Translation\Formatter\MessageFormatterInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

// Help opcache.preload discover always-needed symbols
class_exists(MessageCatalogue::class);

/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Translator implements TranslatorInterface, TranslatorBagInterface, LocaleAwareInterface
{
    /**
     * @var MessageCatalogueInterface[]
     */
    protected $catalogues = [];

    private string $locale;

    /**
     * @var string[]
     */
    private array $fallbackLocales = [];

    /**
     * @var LoaderInterface[]
     */
    private array $loaders = [];

    private array $resources = [];

    private MessageFormatterInterface $formatter;

    private ?string $cacheDir;

    private bool $debug;

    private array $cacheVary;

    private ?ConfigCacheFactoryInterface $configCacheFactory;

    private array $parentLocales;

    private bool $hasIntlFormatter;

    /**
     * @throws InvalidArgumentException If a locale contains invalid characters
     */
    public function __construct(string $locale, ?MessageFormatterInterface $formatter = null, ?string $cacheDir = null, bool $debug = false, array $cacheVary = [])
    {
        $this->setLocale($locale);

        $this->formatter = $formatter ??= new MessageFormatter();
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
        $this->cacheVary = $cacheVary;
        $this->hasIntlFormatter = $formatter instanceof IntlFormatterInterface;
    }

    /**
     * @return void
     */
    public function setConfigCacheFactory(ConfigCacheFactoryInterface $configCacheFactory)
    {
        $this->configCacheFactory = $configCacheFactory;
    }

    /**
     * Adds a Loader.
     *
     * @param string $format The name of the loader (@see addResource())
     *
     * @return void
     */
    public function addLoader(string $format, LoaderInterface $loader)
    {
        $this->loaders[$format] = $loader;
    }

    /**
     * Adds a Resource.
     *
     * @param string $format   The name of the loader (@see addLoader())
     * @param mixed  $resource The resource name
     *
     * @return void
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     */
    public function addResource(string $format, mixed $resource, string $locale, ?string $domain = null)
    {
        $domain ??= 'messages';

        $this->assertValidLocale($locale);
        $locale ?: $locale = class_exists(\Locale::class) ? \Locale::getDefault() : 'en';

        $this->resources[$locale][] = [$format, $resource, $domain];