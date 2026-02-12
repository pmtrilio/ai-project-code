use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Uri;
use RuntimeException;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method array validate(array $rules, ...$params)
 * @method array validateWithBag(string $errorBag, array $rules, ...$params)
 * @method bool hasValidSignature(bool $absolute = true)
 * @method bool hasValidRelativeSignature()
 * @method bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
 * @method bool hasValidRelativeSignatureWhileIgnoring($ignoreQuery = [])
 */
class Request extends SymfonyRequest implements Arrayable, ArrayAccess
{
    use Concerns\CanBePrecognitive,
        Concerns\InteractsWithContentTypes,
        Concerns\InteractsWithFlashData,
        Concerns\InteractsWithInput,
        Conditionable,
        Macroable;

    /**
     * The decoded JSON content for the request.
     *
     * @var \Symfony\Component\HttpFoundation\InputBag|null
     */
    protected $json;

    /**
     * All of the converted files for the request.
     *
     * @var array<int, \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]>
     */
    protected $convertedFiles;

    /**
     * The user resolver callback.
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * The route resolver callback.
     *
     * @var \Closure
     */
    protected $routeResolver;

    /**
     * The cached "Accept" header value.
     *
     * @var string|null
     */
    protected $cachedAcceptHeader;

    /**
     * Create a new Illuminate HTTP request from server variables.
     *
     * @return static
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        return static::createFromBase(SymfonyRequest::createFromGlobals());
    }

    /**
     * Return the Request instance.
     *
     * @return $this
     */
    public function instance()
    {
        return $this;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method()
    {
        return $this->getMethod();
    }

    /**
     * Get a URI instance for the request.
     *
     * @return \Illuminate\Support\Uri
     */
    public function uri()