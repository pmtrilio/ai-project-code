
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