 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Database\Schema;

use Cake\Database\Connection;
use Cake\Database\Exception\DatabaseException;

/**
 * Represents a single table in a database schema.
 *
 * Can either be populated using the reflection API's
 * or by incrementally building an instance using
 * methods.
 *
 * Once created TableSchema instances can be added to
 * Schema\Collection objects. They can also be converted into SQL using the
 * createSql(), dropSql() and truncateSql() methods.
 */
class TableSchema implements TableSchemaInterface, SqlGeneratorInterface
{
    /**
     * The name of the table
     *
     * @var string
     */
    protected string $_table;

    /**
     * Columns in the table.
     *
     * @var array<string, \Cake\Database\Schema\Column>
     */
    protected array $_columns = [];

    /**
     * A map with columns to types
     *
     * @var array<string, string>
     */
    protected array $_typeMap = [];

    /**
     * Indexes in the table.
     *
     * @var array<string, \Cake\Database\Schema\Index>
     */
    protected array $_indexes = [];

    /**
     * Constraints in the table.
     *
     * @var array<string, \Cake\Database\Schema\Constraint>
     */
    protected array $_constraints = [];

    /**
     * Options for the table.
     *
     * @var array<string, mixed>
     */
    protected array $_options = [];

    /**
     * Whether the table is temporary
     *
     * @var bool
     */
    protected bool $_temporary = false;

    /**
     * Column length when using a `tiny` column type
     *
     * @var int
     */
    public const LENGTH_TINY = 255;

    /**
     * Column length when using a `medium` column type
     *
     * @var int
     */
    public const LENGTH_MEDIUM = 16777215;

    /**
     * Column length when using a `long` column type
     *
     * @var int
     */
    public const LENGTH_LONG = 4294967295;

    /**