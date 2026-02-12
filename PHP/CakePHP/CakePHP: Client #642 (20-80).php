use Cake\Core\InstanceConfigTrait;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\Client\Adapter\Curl;
use Cake\Http\Client\Adapter\Mock as MockAdapter;
use Cake\Http\Client\Adapter\Stream;
use Cake\Http\Client\AdapterInterface;
use Cake\Http\Client\ClientEvent;
use Cake\Http\Client\Request;
use Cake\Http\Client\Response;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Cookie\CookieInterface;
use Cake\Utility\Hash;
use InvalidArgumentException;
use Laminas\Diactoros\Uri;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The end user interface for doing HTTP requests.
 *
 * ### Scoped clients
 *
 * If you're doing multiple requests to the same hostname it's often convenient
 * to use the constructor arguments to create a scoped client. This allows you
 * to keep your code DRY and not repeat hostnames, authentication, and other options.
 *
 * ### Doing requests
 *
 * Once you've created an instance of Client you can do requests
 * using several methods. Each corresponds to a different HTTP method.
 *
 * - get()
 * - post()
 * - put()
 * - delete()
 * - patch()
 *
 * ### Cookie management
 *
 * Client will maintain cookies from the responses done with
 * a client instance. These cookies will be automatically added
 * to future requests to matching hosts. Cookies will respect the
 * `Expires`, `Path` and `Domain` attributes. You can get the client's
 * CookieCollection using cookies()
 *
 * You can use the 'cookieJar' constructor option to provide a custom
 * cookie jar instance you've restored from cache/disk. By default,
 * an empty instance of {@link \Cake\Http\Client\CookieCollection} will be created.
 *
 * ### Sending request bodies
 *
 * By default, any POST/PUT/PATCH/DELETE request with $data will
 * send their data as `application/x-www-form-urlencoded` unless
 * there are attached files. In that case `multipart/form-data`
 * will be used.
 *
 * When sending request bodies you can use the `type` option to
 * set the Content-Type for the request:
 *