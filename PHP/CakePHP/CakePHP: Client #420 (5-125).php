 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Http;

use Cake\Core\App;
use Cake\Core\Exception\CakeException;
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
 * ```
 * $http->get('/users', [], ['type' => 'json']);
 * ```
 *
 * The `type` option sets both the `Content-Type` and `Accept` header, to
 * the same mime type. When using `type` you can use either a full mime
 * type or an alias. If you need different types in the Accept and Content-Type
 * headers you should set them manually and not use `type`
 *
 * ### Using authentication
 *
 * By using the `auth` key you can use authentication. The type sub option
 * can be used to specify which authentication strategy you want to use.
 * CakePHP comes with a few built-in strategies:
 *
 * - Basic
 * - Digest
 * - Oauth
 *
 * ### Using proxies
 *
 * By using the `proxy` key you can set authentication credentials for
 * a proxy if you need to use one. The type sub option can be used to
 * specify which authentication strategy you want to use.
 * CakePHP comes with built-in support for basic authentication.
 *
 * @implements \Cake\Event\EventDispatcherInterface<\Cake\Http\Client>
 */
class Client implements EventDispatcherInterface, ClientInterface
{
    /**
     * @use \Cake\Event\EventDispatcherTrait<\Cake\Http\Client>
     */
    use EventDispatcherTrait;
    use InstanceConfigTrait;

    /**
     * Default configuration for the client.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'auth' => null,
        'adapter' => null,
        'host' => null,