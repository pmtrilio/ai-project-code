 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class HttpClient
{
    /**
     * @param array $defaultOptions     Default request's options
     * @param int   $maxHostConnections The maximum number of connections to a single host
     * @param int   $maxPendingPushes   The maximum number of pushed responses to accept in the queue
     *
     * @see HttpClientInterface::OPTIONS_DEFAULTS for available options
     */
    public static function create(array $defaultOptions = [], int $maxHostConnections = 6, int $maxPendingPushes = 50): HttpClientInterface
    {
        if ($amp = class_exists(ConnectionLimitingPool::class) && interface_exists(Promise::class)) {
            if (!\extension_loaded('curl')) {
                return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
            }

            // Skip curl when HTTP/2 push is unsupported or buggy, see https://bugs.php.net/77535
            if (!\defined('CURLMOPT_PUSHFUNCTION')) {
                return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
            }

            static $curlVersion = null;
            $curlVersion ??= curl_version();

            // HTTP/2 push crashes before curl 7.61
            if (0x073D00 > $curlVersion['version_number'] || !(\CURL_VERSION_HTTP2 & $curlVersion['features'])) {
                return new AmpHttpClient($defaultOptions, null, $maxHostConnections, $maxPendingPushes);
            }
        }

        if (\extension_loaded('curl')) {
            if ('\\' !== \DIRECTORY_SEPARATOR || isset($defaultOptions['cafile']) || isset($defaultOptions['capath']) || \ini_get('curl.cainfo') || \ini_get('openssl.cafile') || \ini_get('openssl.capath')) {
                return new CurlHttpClient($defaultOptions, $maxHostConnections, $maxPendingPushes);
            }

            @trigger_error('Configure the "curl.cainfo", "openssl.cafile" or "openssl.capath" php.ini setting to enable the CurlHttpClient', \E_USER_WARNING);
        }
