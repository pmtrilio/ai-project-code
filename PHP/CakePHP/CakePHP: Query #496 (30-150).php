
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
        'set' => [],
        'insert' => [],
        'values' => [],
        'with' => [],
        'optimizerHint' => [],
        'select' => [],
        'distinct' => false,
        'modifier' => [],
        'from' => [],
        'join' => [],
        'where' => null,
        'group' => [],
        'having' => null,
        'window' => [],
        'order' => null,
        'limit' => null,
        'offset' => null,
        'union' => [],
        'epilog' => null,
        'intersect' => [],
    ];

    /**
     * Indicates whether internal state of this query was changed, this is used to
     * discard internal cached objects such as the transformed query or the reference
     * to the executed statement.
     *
     * @var bool
     */
    protected bool $_dirty = false;

    /**
     * @var \Cake\Database\StatementInterface|null
     */
    protected ?StatementInterface $_statement = null;

    /**
     * The object responsible for generating query placeholders and temporarily store values
     * associated to each of those.
     *
     * @var \Cake\Database\ValueBinder|null
     */
    protected ?ValueBinder $_valueBinder = null;

    /**