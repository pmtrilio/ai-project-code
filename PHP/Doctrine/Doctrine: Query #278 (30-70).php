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