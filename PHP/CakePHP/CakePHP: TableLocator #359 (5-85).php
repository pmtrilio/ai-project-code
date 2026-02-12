 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\ORM\Locator;

use Cake\Core\App;
use Cake\Database\Exception\DatabaseException;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Locator\AbstractLocator;
use Cake\Datasource\RepositoryInterface;
use Cake\ORM\AssociationCollection;
use Cake\ORM\Exception\MissingTableClassException;
use Cake\ORM\Query\QueryFactory;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use function Cake\Core\pluginSplit;

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