use Laminas\Stdlib\RequestInterface as Request;
use Traversable;

use function array_merge;
use function count;
use function is_array;
use function method_exists;
use function preg_match;
use function preg_quote;
use function rawurldecode;
use function rawurlencode;
use function sprintf;
use function str_replace;
use function strlen;
use function strtr;

/**
 * Segment route.
 */
class Segment implements RouteInterface
{
    /**
     * Cache for the encode output.
     *
     * @var array<string, string>
     */
    protected static $cacheEncode = [];

    /**
     * Map of allowed special chars in path segments.
     *
     * http://tools.ietf.org/html/rfc3986#appendix-A
     * segement      = *pchar
     * pchar         = unreserved / pct-encoded / sub-delims / ":" / "@"
     * unreserved    = ALPHA / DIGIT / "-" / "." / "_" / "~"
     * sub-delims    = "!" / "$" / "&" / "'" / "(" / ")"
     *               / "*" / "+" / "," / ";" / "="
     *
     * @var array<string, string>
     */
    protected static $urlencodeCorrectionMap = [