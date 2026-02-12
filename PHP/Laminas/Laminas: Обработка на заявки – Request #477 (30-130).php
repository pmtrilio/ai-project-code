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
     * @param  string $string
     * @param  bool $allowCustomMethods
     * @throws Exception\InvalidArgumentException
     * @return static
     */
    public static function fromString($string, $allowCustomMethods = true)
    {
        $request = new static();
        $request->setAllowCustomMethods($allowCustomMethods);

        $lines = explode("\r\n", $string);

        // first line must be Method/Uri/Version string
        $matches = null;
        $methods = $allowCustomMethods
            ? '[\w-]+'
            : implode(
                '|',
                [
                    self::METHOD_OPTIONS,
                    self::METHOD_GET,
                    self::METHOD_HEAD,
                    self::METHOD_POST,
                    self::METHOD_PUT,
                    self::METHOD_DELETE,
                    self::METHOD_TRACE,
                    self::METHOD_CONNECT,
                    self::METHOD_PATCH,
                ]
            );

        $regex     = '#^(?P<method>' . $methods . ')\s(?P<uri>[^ ]*)(?:\sHTTP\/(?P<version>\d+\.\d+)){0,1}#';
        $firstLine = array_shift($lines);
        if (! preg_match($regex, $firstLine, $matches)) {
            throw new Exception\InvalidArgumentException(
                'A valid request line was not found in the provided string'
            );
        }

        $request->setMethod($matches['method']);
        $request->setUri($matches['uri']);

        $parsedUri = parse_url($matches['uri']);
        if (array_key_exists('query', $parsedUri)) {
            $parsedQuery = [];
            parse_str($parsedUri['query'], $parsedQuery);
            $request->setQuery(new Parameters($parsedQuery));
        }

        if (isset($matches['version'])) {
            $request->setVersion($matches['version']);
        }

        if (empty($lines)) {
            return $request;
        }

        $isHeader = true;
        $headers  = $rawBody = [];
        while ($lines) {