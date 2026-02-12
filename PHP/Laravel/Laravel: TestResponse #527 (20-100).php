use Illuminate\Support\Traits\Tappable;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Testing\Constraints\SeeInOrder;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponseAssert as PHPUnit;
use LogicException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\StreamedJsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @template TResponse of \Symfony\Component\HttpFoundation\Response
 *
 * @mixin \Illuminate\Http\Response
 */
class TestResponse implements ArrayAccess
{
    use Concerns\AssertsStatusCodes, Conditionable, Dumpable, Tappable, Macroable {
        __call as macroCall;
    }

    /**
     * The original request.
     *
     * @var \Illuminate\Http\Request|null
     */
    public $baseRequest;

    /**
     * The response to delegate to.
     *
     * @var TResponse
     */
    public $baseResponse;

    /**
     * The collection of logged exceptions for the request.
     *
     * @var \Illuminate\Support\Collection
     */
    public $exceptions;

    /**
     * The streamed content of the response.
     *
     * @var string
     */
    protected $streamedContent;

    /**
     * Create a new test response instance.
     *
     * @param  TResponse  $response
     * @param  \Illuminate\Http\Request|null  $request
     */
    public function __construct($response, $request = null)
    {
        $this->baseResponse = $response;
        $this->baseRequest = $request;
        $this->exceptions = new Collection;
    }

    /**
     * Create a new TestResponse from another response.
     *
     * @template R of TResponse
     *
     * @param  R  $response
     * @param  \Illuminate\Http\Request|null  $request
     * @return static<R>
     */
    public static function fromBaseResponse($response, $request = null)
    {
        return new static($response, $request);
    }

    /**
     * Assert that the response has a successful status code.
     *
     * @return $this
     */