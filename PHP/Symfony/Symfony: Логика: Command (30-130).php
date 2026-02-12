/**
 * Base class for all commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Command
{
    // see https://tldp.org/LDP/abs/html/exitcodes.html
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    /**
     * @var string|null The default command name
     *
     * @deprecated since Symfony 6.1, use the AsCommand attribute instead
     */
    protected static $defaultName;

    /**
     * @var string|null The default command description
     *
     * @deprecated since Symfony 6.1, use the AsCommand attribute instead
     */
    protected static $defaultDescription;

    private ?Application $application = null;
    private ?string $name = null;
    private ?string $processTitle = null;
    private array $aliases = [];
    private InputDefinition $definition;
    private bool $hidden = false;
    private string $help = '';
    private string $description = '';
    private ?InputDefinition $fullDefinition = null;
    private bool $ignoreValidationErrors = false;
    private ?\Closure $code = null;
    private array $synopsis = [];
    private array $usages = [];
    private ?HelperSet $helperSet = null;

    public static function getDefaultName(): ?string
    {
        $class = static::class;

        if ($attribute = (new \ReflectionClass($class))->getAttributes(AsCommand::class)) {
            return $attribute[0]->newInstance()->name;
        }

        $r = new \ReflectionProperty($class, 'defaultName');

        if ($class !== $r->class || null === static::$defaultName) {
            return null;
        }

        trigger_deprecation('symfony/console', '6.1', 'Relying on the static property "$defaultName" for setting a command name is deprecated. Add the "%s" attribute to the "%s" class instead.', AsCommand::class, static::class);

        return static::$defaultName;
    }

    public static function getDefaultDescription(): ?string
    {
        $class = static::class;

        if ($attribute = (new \ReflectionClass($class))->getAttributes(AsCommand::class)) {
            return $attribute[0]->newInstance()->description;
        }

        $r = new \ReflectionProperty($class, 'defaultDescription');

        if ($class !== $r->class || null === static::$defaultDescription) {
            return null;
        }

        trigger_deprecation('symfony/console', '6.1', 'Relying on the static property "$defaultDescription" for setting a command description is deprecated. Add the "%s" attribute to the "%s" class instead.', AsCommand::class, static::class);

        return static::$defaultDescription;
    }

    /**
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(?string $name = null)
    {
        $this->definition = new InputDefinition();

        if (null === $name && null !== $name = static::getDefaultName()) {
            $aliases = explode('|', $name);

            if ('' === $name = array_shift($aliases)) {
                $this->setHidden(true);
                $name = array_shift($aliases);
            }

            $this->setAliases($aliases);
        }

        if (null !== $name) {
            $this->setName($name);