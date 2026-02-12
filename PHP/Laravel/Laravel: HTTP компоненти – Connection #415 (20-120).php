use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Traits\Macroable;
use PDO;
use PDOStatement;
use RuntimeException;

use function Illuminate\Support\enum_value;

class Connection implements ConnectionInterface
{
    use DetectsConcurrencyErrors,
        DetectsLostConnections,
        Concerns\ManagesTransactions,
        InteractsWithTime,
        Macroable;

    /**
     * The active PDO connection.
     *
     * @var \PDO|(\Closure(): \PDO)
     */
    protected $pdo;

    /**
     * The active PDO connection used for reads.
     *
     * @var \PDO|(\Closure(): \PDO)
     */
    protected $readPdo;

    /**
     * The database connection configuration options for reading.
     *
     * @var array
     */
    protected $readPdoConfig = [];

    /**
     * The name of the connected database.
     *
     * @var string
     */
    protected $database;

    /**
     * The type of the connection.
     *
     * @var string|null
     */
    protected $readWriteType;

    /**
     * The table prefix for the connection.
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * The database connection configuration options.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The reconnector instance for the connection.
     *
     * @var (callable(\Illuminate\Database\Connection): mixed)
     */
    protected $reconnector;

    /**
     * The query grammar implementation.
     *
     * @var \Illuminate\Database\Query\Grammars\Grammar
     */
    protected $queryGrammar;

    /**
     * The schema grammar implementation.
     *
     * @var \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected $schemaGrammar;

    /**
     * The query post processor implementation.
     *
     * @var \Illuminate\Database\Query\Processors\Processor
     */
    protected $postProcessor;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;