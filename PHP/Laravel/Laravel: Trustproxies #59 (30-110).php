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
     */
    protected function setTrustedProxyIpAddresses(Request $request)
    {
        $trustedIps = $this->proxies() ?: config('trustedproxy.proxies');

        if (is_null($trustedIps) &&
            (laravel_cloud() ||
             str_ends_with($request->host(), '.on-forge.com') ||
             str_ends_with($request->host(), '.on-vapor.com'))) {
            $trustedIps = '*';
        }

        if (str_ends_with($request->host(), '.on-forge.com') ||
            str_ends_with($request->host(), '.on-vapor.com')) {
            $request->headers->remove('X-Forwarded-Host');
        }

        if ($trustedIps === '*' || $trustedIps === '**') {
            return $this->setTrustedProxyIpAddressesToTheCallingIp($request);
        }

        $trustedIps = is_string($trustedIps)
            ? array_map(trim(...), explode(',', $trustedIps))
            : $trustedIps;

        if (is_array($trustedIps)) {
            return $this->setTrustedProxyIpAddressesToSpecificIps($request, $trustedIps);
        }
    }

    /**
     * Specify the IP addresses to trust explicitly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $trustedIps
     * @return void
     */
    protected function setTrustedProxyIpAddressesToSpecificIps(Request $request, array $trustedIps)
    {
        $request->setTrustedProxies(array_reduce($trustedIps, function ($ips, $trustedIp) use ($request) {
            $ips[] = $trustedIp === 'REMOTE_ADDR'
                ? $request->server->get('REMOTE_ADDR')
                : $trustedIp;

            return $ips;