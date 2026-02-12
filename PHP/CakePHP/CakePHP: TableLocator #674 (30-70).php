
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