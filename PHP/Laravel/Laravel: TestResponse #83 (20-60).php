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