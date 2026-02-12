use Cake\Database\Query\UpdateQuery;
use Cake\Database\Retry\ReconnectStrategy;
use Cake\Database\Schema\CachedCollection;
use Cake\Database\Schema\Collection as SchemaCollection;
use Cake\Database\Schema\CollectionInterface as SchemaCollectionInterface;
use Cake\Datasource\ConnectionInterface;
use Cake\Log\Log;
use Closure;
use Psr\SimpleCache\CacheInterface;
use Throwable;
use function Cake\Core\env;

/**
 * Represents a connection with a database server.
 */
class Connection implements ConnectionInterface
{
    /**
     * Contains the configuration params for this connection.
     *
     * @var array<string, mixed>
     */
    protected array $_config;

    /**
     * @var \Cake\Database\Driver
     */
    protected Driver $readDriver;

    /**
     * @var \Cake\Database\Driver
     */
    protected Driver $writeDriver;

    /**
     * Contains how many nested transactions have been started.
     *
     * @var int
     */
    protected int $_transactionLevel = 0;
