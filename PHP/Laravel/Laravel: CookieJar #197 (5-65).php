use Illuminate\Contracts\Cookie\QueueingFactory as JarContract;
use Illuminate\Support\Arr;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\HttpFoundation\Cookie;

class CookieJar implements JarContract
{
    use InteractsWithTime, Macroable;

    /**
     * The default path (if specified).
     *
     * @var string
     */
    protected $path = '/';

    /**
     * The default domain (if specified).
     *
     * @var string|null
     */
    protected $domain;

    /**
     * The default secure setting (defaults to null).
     *
     * @var bool|null
     */
    protected $secure;

    /**
     * The default SameSite option (defaults to lax).
     *
     * @var string
     */
    protected $sameSite = 'lax';

    /**
     * All of the cookies queued for sending.
     *
     * @var \Symfony\Component\HttpFoundation\Cookie[]
     */
    protected $queued = [];

    /**
     * Create a new cookie instance.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int  $minutes
     * @param  string|null  $path
     * @param  string|null  $domain
     * @param  bool|null  $secure
     * @param  bool  $httpOnly
     * @param  bool  $raw
     * @param  string|null  $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
    {