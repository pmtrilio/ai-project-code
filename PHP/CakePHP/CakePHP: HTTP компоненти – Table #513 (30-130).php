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
 *
 * ### Retrieving data
 *
 * The primary way to retrieve data is using Table::find(). See that method
 * for more information.
 *
 * ### Dynamic finders
 *
 * In addition to the standard find($type) finder methods, CakePHP provides dynamic
 * finder methods. These methods allow you to easily set basic conditions up. For example
 * to filter users by username you would call
 *
 * ```
 * $query = $users->findByUsername('mark');
 * ```
 *
 * You can also combine conditions on multiple fields using either `Or` or `And`:
 *
 * ```
 * $query = $users->findByUsernameOrEmail('mark', 'mark@example.org');
 * ```
 *
 * ### Bulk updates/deletes
 *
 * You can use Table::updateAll() and Table::deleteAll() to do bulk updates/deletes.
 * You should be aware that events will *not* be fired for bulk updates/deletes.
 *
 * ### Events
 *
 * Table objects emit several events during as life-cycle hooks during find, delete and save
 * operations. All events use the CakePHP event package:
 *
 * - `Model.beforeFind` Fired before each find operation. By stopping the event and
 *   supplying a return value you can bypass the find operation entirely. Any
 *   changes done to the $query instance will be retained for the rest of the find. The
 *   `$primary` parameter indicates whether this is the root query, or an
 *   associated query.
 *
 * - `Model.buildValidator` Allows listeners to modify validation rules
 *   for the provided named validator.
 *
 * - `Model.buildRules` Allows listeners to modify the rules checker by adding more rules.
 *   Behaviors or custom listeners can subscribe to this event. For tables you don't
 *   need to subscribe to this event, simply override the `Table::buildRules()` method.
 *
 * - `Model.beforeRules` Fired before an entity is validated using the rules checker.
 *   By stopping this event, you can return the final value of the rules checking operation.
 *
 * - `Model.afterRules` Fired after the rules have been checked on the entity. By
 *   stopping this event, you can return the final value of the rules checking operation.
 *
 * - `Model.beforeSave` Fired before each entity is saved. Stopping this event will
 *   abort the save operation. When the event is stopped the result of the event will be returned.
 *
 * - `Model.afterSave` Fired after an entity is saved.
 *
 * - `Model.afterSaveCommit` Fired after the transaction in which the save operation is
 *   wrapped has been committed. It’s also triggered for non atomic saves where database
 *   operations are implicitly committed. The event is triggered only for the primary
 *   table on which save() is directly called. The event is not triggered if a