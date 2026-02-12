use Cake\Datasource\ConnectionManager;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\RulesAwareTrait;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Exception\MissingEntityException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\Exception\RolledbackTransactionException;
use Cake\ORM\Query\DeleteQuery;
use Cake\ORM\Query\InsertQuery;
use Cake\ORM\Query\QueryFactory;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Query\UpdateQuery;
use Cake\ORM\Rule\IsUnique;
use Cake\Utility\Inflector;
use Cake\Validation\ValidatorAwareInterface;
use Cake\Validation\ValidatorAwareTrait;
use Closure;
use Exception;
use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use ReflectionFunction;
use ReflectionNamedType;
use function Cake\Core\deprecationWarning;
use function Cake\Core\namespaceSplit;

/**
 * Represents a single database table.
 *
 * Exposes methods for retrieving data out of it, and manages the associations
 * this table has to other tables. Multiple instances of this class can be created
 * for the same database table with different aliases, this allows you to address
 * your database structure in a richer and more expressive way.