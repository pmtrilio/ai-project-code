 * as it is shown in the following example:
 *
 * ```
 * 'response' => [
 *     'format' => yii\web\Response::FORMAT_JSON,
 *     'charset' => 'UTF-8',
 *     // ...
 * ]
 * ```
 *
 * For more details and usage information on Response, see the [guide article on responses](guide:runtime-responses).
 *
 * @property-read CookieCollection $cookies The cookie collection.
 * @property-write string $downloadHeaders The attachment file name.
 * @property-read HeaderCollection $headers The header collection.
 * @property-read bool $isClientError Whether this response indicates a client error.
 * @property-read bool $isEmpty Whether this response is empty.
 * @property-read bool $isForbidden Whether this response indicates the current request is forbidden.
 * @property-read bool $isInformational Whether this response is informational.
 * @property-read bool $isInvalid Whether this response has a valid [[statusCode]].
 * @property-read bool $isNotFound Whether this response indicates the currently requested resource is not
 * found.
 * @property-read bool $isOk Whether this response is OK.
 * @property-read bool $isRedirection Whether this response is a redirection.
 * @property-read bool $isServerError Whether this response indicates a server error.
 * @property-read bool $isSuccessful Whether this response is successful.
 * @property int $statusCode The HTTP status code to send with the response.
 * @property-write \Throwable $statusCodeByException The exception object.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class Response extends \yii\base\Response
{
    /**
     * @event \yii\base\Event an event that is triggered at the beginning of [[send()]].
     */
    public const EVENT_BEFORE_SEND = 'beforeSend';
    /**
     * @event \yii\base\Event an event that is triggered at the end of [[send()]].
     */
    public const EVENT_AFTER_SEND = 'afterSend';
    /**
     * @event \yii\base\Event an event that is triggered right after [[prepare()]] is called in [[send()]].
     * You may respond to this event to filter the response content before it is sent to the client.
     */
    public const EVENT_AFTER_PREPARE = 'afterPrepare';
    public const FORMAT_RAW = 'raw';
    public const FORMAT_HTML = 'html';
    public const FORMAT_JSON = 'json';
    public const FORMAT_JSONP = 'jsonp';
    public const FORMAT_XML = 'xml';
    /**
     * @var string the response format. This determines how to convert [[data]] into [[content]]
     * when the latter is not set. The value of this property must be one of the keys declared in the [[formatters]] array.
     * By default, the following formats are supported:
     *
     * - [[FORMAT_RAW]]: the data will be treated as the response content without any conversion.
     *   No extra HTTP header will be added.
     * - [[FORMAT_HTML]]: the data will be treated as the response content without any conversion.