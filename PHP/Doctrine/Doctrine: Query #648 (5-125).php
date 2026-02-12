namespace Doctrine\ORM;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\Psr6\CacheAdapter;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Types\Type;
use Doctrine\Deprecations\Deprecation;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\AST\DeleteStatement;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\AST\UpdateStatement;
use Doctrine\ORM\Query\Exec\AbstractSqlExecutor;
use Doctrine\ORM\Query\Exec\SqlFinalizer;
use Doctrine\ORM\Query\OutputWalker;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ParameterTypeInferer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\ParserResult;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Utility\HierarchyDiscriminatorResolver;
use Psr\Cache\CacheItemPoolInterface;

use function array_keys;
use function array_values;
use function assert;
use function count;
use function get_debug_type;
use function in_array;
use function is_a;
use function is_int;
use function ksort;
use function md5;
use function method_exists;
use function reset;
use function serialize;
use function sha1;
use function stripos;

/**
 * A Query object represents a DQL query.
 *
 * @final
 */
class Query extends AbstractQuery
{
    /**
     * A query object is in CLEAN state when it has NO unparsed/unprocessed DQL parts.
     */
    public const STATE_CLEAN = 1;

    /**
     * A query object is in state DIRTY when it has DQL parts that have not yet been
     * parsed/processed. This is automatically defined as DIRTY when addDqlQueryPart
     * is called.
     */
    public const STATE_DIRTY = 2;

    /* Query HINTS */

    /**
     * The refresh hint turns any query into a refresh query with the result that
     * any local changes in entities are overridden with the fetched values.
     */
    public const HINT_REFRESH = 'doctrine.refresh';

    public const HINT_CACHE_ENABLED = 'doctrine.cache.enabled';

    public const HINT_CACHE_EVICT = 'doctrine.cache.evict';

    /**
     * Internal hint: is set to the proxy entity that is currently triggered for loading
     */
    public const HINT_REFRESH_ENTITY = 'doctrine.refresh.entity';

    /**
     * The forcePartialLoad query hint forces a particular query to return
     * partial objects.
     *
     * @todo Rename: HINT_OPTIMIZE
     */
    public const HINT_FORCE_PARTIAL_LOAD = 'doctrine.forcePartialLoad';

    /**
     * The includeMetaColumns query hint causes meta columns like foreign keys and
     * discriminator columns to be selected and returned as part of the query result.
     *
     * This hint does only apply to non-object queries.
     */
    public const HINT_INCLUDE_META_COLUMNS = 'doctrine.includeMetaColumns';

    /**
     * An array of class names that implement \Doctrine\ORM\Query\TreeWalker and
     * are iterated and executed after the DQL has been parsed into an AST.
     */
    public const HINT_CUSTOM_TREE_WALKERS = 'doctrine.customTreeWalkers';

    /**
     * A string with a class name that implements \Doctrine\ORM\Query\TreeWalker
     * and is used for generating the target SQL from any DQL AST tree.
     */
    public const HINT_CUSTOM_OUTPUT_WALKER = 'doctrine.customOutputWalker';

    /**
     * Marks queries as creating only read only objects.
     *
     * If the object retrieved from the query is already in the identity map
     * then it does not get marked as read only if it wasn't already.
     */
    public const HINT_READ_ONLY = 'doctrine.readOnly';

    public const HINT_INTERNAL_ITERATION = 'doctrine.internal.iteration';

    public const HINT_LOCK_MODE = 'doctrine.lockMode';

    /**
     * The current state of this query.