use ArrayIterator;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Headers;
use Laminas\Stdlib\Parameters;
use Laminas\Stdlib\ParametersInterface;
use Laminas\Stdlib\RequestInterface;
use Laminas\Uri\Exception as UriException;
use Laminas\Uri\Http as HttpUri;

use function array_key_exists;
use function array_shift;
use function defined;
use function explode;
use function implode;
use function is_string;
use function parse_str;
use function parse_url;
use function preg_match;
use function sprintf;
use function stristr;
use function strtoupper;

/**
 * HTTP Request
 *
 * @link      http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5
 */
class Request extends AbstractMessage implements RequestInterface
{
    /**#@+
     *
     * @const string METHOD constant names
     */
    public const METHOD_OPTIONS  = 'OPTIONS';
    public const METHOD_GET      = 'GET';
    public const METHOD_HEAD     = 'HEAD';
    public const METHOD_POST     = 'POST';
    public const METHOD_PUT      = 'PUT';
    public const METHOD_DELETE   = 'DELETE';
    public const METHOD_TRACE    = 'TRACE';
    public const METHOD_CONNECT  = 'CONNECT';
    public const METHOD_PATCH    = 'PATCH';
    public const METHOD_PROPFIND = 'PROPFIND';
    /**#@-*/

    /** @var string */
    protected $method = self::METHOD_GET;

    /** @var bool */
    protected $allowCustomMethods = true;

    /** @var string|HttpUri */
    protected $uri;

    /** @var ParametersInterface */
    protected $queryParams;

    /** @var ParametersInterface */
    protected $postParams;

    /** @var ParametersInterface */