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