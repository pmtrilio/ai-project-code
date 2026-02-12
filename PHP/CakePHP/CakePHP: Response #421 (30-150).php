 *
 * ### Get header values
 *
 * Header names are case-insensitive, but normalized to Title-Case
 * when the response is parsed.
 *
 * ```
 * $val = $response->getHeaderLine('content-type');
 * ```
 *
 * Will read the Content-Type header. You can get all set
 * headers using:
 *
 * ```
 * $response->getHeaders();
 * ```
 *
 * ### Get the response body
 *
 * You can access the response body stream using:
 *
 * ```
 * $content = $response->getBody();
 * ```
 *
 * You can get the body string using:
 *
 * ```
 * $content = $response->getStringBody();
 * ```
 *
 * If your response body is in XML or JSON you can use
 * special content type specific accessors to read the decoded data.
 * JSON data will be returned as arrays, while XML data will be returned
 * as SimpleXML nodes:
 *
 * ```
 * // Get as XML
 * $content = $response->getXml()
 * // Get as JSON
 * $content = $response->getJson()
 * ```
 *
 * If the response cannot be decoded, null will be returned.
 *
 * ### Check the status code
 *
 * You can access the response status code using:
 *
 * ```
 * $content = $response->getStatusCode();
 * ```
 */
class Response extends Message implements ResponseInterface
{
    use MessageTrait;

    /**
     * The status code of the response.
     *
     * @var int
     */
    protected int $code = 0;

    /**
     * Cookie Collection instance
     *
     * @var \Cake\Http\Cookie\CookieCollection|null
     */
    protected ?CookieCollection $cookies = null;

    /**
     * The reason phrase for the status code
     *
     * @var string
     */
    protected string $reasonPhrase;

    /**
     * Cached decoded XML data.
     *
     * @var \SimpleXMLElement|null
     */
    protected ?SimpleXMLElement $_xml = null;

    /**
     * Cached decoded JSON data.
     *
     * @var mixed
     */
    protected mixed $_json = null;

    /**
     * Constructor
     *
     * @param array<string> $headers Unparsed headers.
     * @param string $body The response body.
     */
    public function __construct(array $headers = [], string $body = '')
    {
        $this->_parseHeaders($headers);
        if ($this->getHeaderLine('Content-Encoding') === 'gzip') {
            $body = $this->_decodeGzipBody($body);
        }
        $stream = new Stream('php://memory', 'wb+');
        $stream->write($body);
        $stream->rewind();
        $this->stream = $stream;
    }

    /**
     * Uncompress a gzip response.
     *
     * Looks for gzip signatures, and if gzinflate() exists,
     * the body will be decompressed.
     *
     * @param string $body Gzip encoded body.
     * @return string
     * @throws \Cake\Core\Exception\CakeException When attempting to decode gzip content without gzinflate.
     */
    protected function _decodeGzipBody(string $body): string