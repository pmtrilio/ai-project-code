use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Utility\Hash;
use Closure;
use InvalidArgumentException;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use function Cake\Core\deprecationWarning;
use function Cake\Core\env;

/**
 * A class that helps wrap Request information and particulars about a single request.
 * Provides methods commonly used to introspect on the request headers and request body.
 */
class ServerRequest implements ServerRequestInterface
{
    /**
     * Array of parameters parsed from the URL.
     *
     * @var array
     */
    protected array $params = [
        'plugin' => null,
        'controller' => null,
        'action' => null,
        '_ext' => null,
        'pass' => [],
    ];

    /**
     * Array of POST data. Will contain form data as well as uploaded files.
     * In PUT/PATCH/DELETE requests this property will contain the form-urlencoded
     * data.
     *
     * @var object|array|null
     */
    protected object|array|null $data = [];

    /**
     * Array of query string arguments
     *
     * @var array
     */
    protected array $query = [];

    /**
     * Array of cookie data.
     *
     * @var array<string, mixed>
     */
    protected array $cookies = [];

    /**
     * Array of environment data.
     *
     * @var array<string, mixed>
     */
    protected array $_environment = [];

    /**
     * Base URL path.
     *
     * @var string
     */
    protected string $base;

    /**
     * webroot path segment for the request.
     *
     * @var string
     */
    protected string $webroot = '/';

    /**
     * Whether to trust HTTP_X headers set by most load balancers.
     * Only set to true if your application runs behind load balancers/proxies
     * that you control.
     *
     * @var bool
     */
    public bool $trustProxy = false;

    /**
     * Trusted proxies list
     *
     * @var array<string>
     */
    protected array $trustedProxies = [];

    /**
     * The built in detectors used with `is()` can be modified with `addDetector()`.
     *
     * There are several ways to specify a detector, see \Cake\Http\ServerRequest::addDetector() for the
     * various formats and ways to define detectors.
     *