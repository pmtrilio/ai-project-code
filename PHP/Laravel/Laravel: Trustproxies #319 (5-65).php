use Closure;
use Illuminate\Http\Request;

class TrustProxies
{
    /**
     * The trusted proxies for the application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The trusted proxies headers for the application.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_PREFIX |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    /**
     * The proxies that have been configured to always be trusted.
     *
     * @var array<int, string>|string|null
     */
    protected static $alwaysTrustProxies;

    /**
     * The proxies headers that have been configured to always be trusted.
     *
     * @var int|null
     */
    protected static $alwaysTrustHeaders;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle(Request $request, Closure $next)
    {
        $request::setTrustedProxies([], $this->getTrustedHeaderNames());

        $this->setTrustedProxyIpAddresses($request);

        return $next($request);
    }

    /**
     * Sets the trusted proxies on the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void