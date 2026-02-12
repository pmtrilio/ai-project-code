use ArrayAccess;
use Closure;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Dumpable;
use Illuminate\Support\Traits\Macroable;
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
    public function assertSuccessful()
    {
        PHPUnit::withResponse($this)->assertTrue(
            $this->isSuccessful(),
            $this->statusMessageWithDetails('>=200, <300', $this->getStatusCode())
        );

        return $this;
    }

    /**
     * Assert that the Precognition request was successful.
     *
     * @return $this
     */
    public function assertSuccessfulPrecognition()
    {
        $this->assertNoContent();

        PHPUnit::withResponse($this)->assertTrue(
            $this->headers->has('Precognition-Success'),
            'Header [Precognition-Success] not present on response.'
        );

        PHPUnit::withResponse($this)->assertSame(