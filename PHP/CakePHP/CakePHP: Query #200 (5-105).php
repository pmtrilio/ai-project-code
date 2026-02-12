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
namespace Cake\Database;

use Cake\Core\Exception\CakeException;
use Cake\Database\Expression\CommonTableExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\OrderByExpression;
use Cake\Database\Expression\OrderClauseExpression;
use Cake\Database\Expression\QueryExpression;
use Closure;
use InvalidArgumentException;
use Stringable;
use Throwable;
use function Cake\Core\deprecationWarning;

/**
 * This class represents a Relational database SQL Query. A query can be of
 * different types like select, update, insert and delete. Exposes the methods
 * for dynamically constructing each query part, execute it and transform it
 * to a specific SQL dialect.
 */
abstract class Query implements ExpressionInterface, Stringable
{
    use TypeMapTrait;

    /**
     * @var string
     */
    public const JOIN_TYPE_INNER = 'INNER';

    /**
     * @var string
     */
    public const JOIN_TYPE_LEFT = 'LEFT';

    /**
     * @var string
     */
    public const JOIN_TYPE_RIGHT = 'RIGHT';

    /**
     * @var string
     */
    public const TYPE_SELECT = 'select';

    /**
     * @var string
     */
    public const TYPE_INSERT = 'insert';

    /**
     * @var string
     */
    public const TYPE_UPDATE = 'update';

    /**
     * @var string
     */
    public const TYPE_DELETE = 'delete';

    /**
     * Connection instance to be used to execute this query.
     *
     * @var \Cake\Database\Connection
     */
    protected Connection $_connection;

    /**
     * Connection role ('read' or 'write')
     *
     * @var string
     */
    protected string $connectionRole = Connection::ROLE_WRITE;

    /**
     * Type of this query (select, insert, update, delete).
     *
     * @var string
     */
    protected string $_type;

    /**
     * List of SQL parts that will be used to build this query.
     *
     * @var array<string, mixed>
     */
    protected array $_parts = [
        'comment' => null,
        'delete' => true,
        'update' => [],