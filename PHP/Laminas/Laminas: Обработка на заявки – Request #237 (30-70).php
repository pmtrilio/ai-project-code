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
    protected $fileParams;

    /**
     * A factory that produces a Request object from a well-formed Http Request string
     *