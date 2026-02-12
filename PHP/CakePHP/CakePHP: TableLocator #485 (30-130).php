
/**
 * Provides a default registry/factory for Table objects.
 *
 * @extends \Cake\Datasource\Locator\AbstractLocator<\Cake\ORM\Table>
 */
class TableLocator extends AbstractLocator implements LocatorInterface
{
    /**
     * Contains a list of locations where table classes should be looked for.
     *
     * @var array<string>
     */
    protected array $locations = [];

    /**
     * Configuration for aliases.
     *
     * @var array<string, array|null>
     */
    protected array $_config = [];

    /**
     * Contains a list of Table objects that were created out of the
     * built-in Table class. The list is indexed by table alias
     *
     * @var array<\Cake\ORM\Table>
     */
    protected array $_fallbacked = [];

    /**
     * Fallback class to use
     *
     * @var class-string<\Cake\ORM\Table>
     */
    protected string $fallbackClassName = Table::class;

    /**
     * Whether fallback class should be used if a table class could not be found.
     *
     * @var bool
     */
    protected bool $allowFallbackClass = true;

    protected QueryFactory $queryFactory;

    /**
     * Constructor.
     *
     * @param array<string>|null $locations Locations where tables should be looked for.
     *   If none provided, the default `Model\Table` under your app's namespace is used.
     */
    public function __construct(?array $locations = null, ?QueryFactory $queryFactory = null)
    {
        if ($locations === null) {
            $locations = [
                'Model/Table',
            ];
        }

        foreach ($locations as $location) {
            $this->addLocation($location);
        }

        $this->queryFactory = $queryFactory ?: new QueryFactory();
    }

    /**
     * Set if fallback class should be used.
     *
     * Controls whether a fallback class should be used to create a table
     * instance if a concrete class for alias used in `get()` could not be found.
     *
     * @param bool $allow Flag to enable or disable fallback
     * @return $this
     */
    public function allowFallbackClass(bool $allow)
    {
        $this->allowFallbackClass = $allow;

        return $this;
    }

    /**
     * Set fallback class name.
     *
     * The class that should be used to create a table instance if a concrete
     * class for alias used in `get()` could not be found. Defaults to
     * `Cake\ORM\Table`.
     *
     * @param class-string<\Cake\ORM\Table> $className Fallback class name
     * @return $this
     */
    public function setFallbackClassName(string $className)
    {
        $this->fallbackClassName = $className;

        return $this;
    }

    /**