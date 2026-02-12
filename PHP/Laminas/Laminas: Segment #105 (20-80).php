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
        '%21' => "!", // sub-delims
        '%24' => "$", // sub-delims
        '%26' => "&", // sub-delims
        '%27' => "'", // sub-delims
        '%28' => "(", // sub-delims
        '%29' => ")", // sub-delims
        '%2A' => "*", // sub-delims
        '%2B' => "+", // sub-delims
        '%2C' => ",", // sub-delims
//      '%2D' => "-", // unreserved - not touched by rawurlencode
//      '%2E' => ".", // unreserved - not touched by rawurlencode
        '%3A' => ":", // pchar
        '%3B' => ";", // sub-delims
        '%3D' => "=", // sub-delims
        '%40' => "@", // pchar
//      '%5F' => "_", // unreserved - not touched by rawurlencode
//      '%7E' => "~", // unreserved - not touched by rawurlencode
    ];

    /**
     * Parts of the route.
     *
     * @var array
     */
    protected $parts;

    /**
     * Regex used for matching the route.
     *
     * @var string