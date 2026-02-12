 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 */
class ServerRequest implements ServerRequestInterface
{
    use RequestTrait;

    private array $attributes = [];

    private array $uploadedFiles;

    /**
     * @param array $serverParams Server parameters, typically from $_SERVER
     * @param array $uploadedFiles Upload file information, a tree of UploadedFiles
     * @param null|string|UriInterface $uri URI for the request, if any.
     * @param null|string $method HTTP method for the request, if any.
     * @param string|resource|StreamInterface $body Message body, if any.
     * @param array<non-empty-string, string|string[]> $headers Headers for the message, if any.
     * @param array $cookieParams Cookies for the message, if any.
     * @param array $queryParams Query params for the message, if any.
     * @param null|array|object $parsedBody The deserialized body parameters, if any.
     * @param string $protocol HTTP protocol version.
     * @throws Exception\InvalidArgumentException For any invalid value.
     */
    public function __construct(
        private array $serverParams = [],
        array $uploadedFiles = [],
        null|string|UriInterface $uri = null,
        ?string $method = null,
        $body = 'php://input',
        array $headers = [],
        private array $cookieParams = [],
        private array $queryParams = [],
        private $parsedBody = null,
        string $protocol = '1.1'
    ) {
        $this->validateUploadedFiles($uploadedFiles);

        if ($body === 'php://input') {
            $body = new Stream($body, 'r');
        }