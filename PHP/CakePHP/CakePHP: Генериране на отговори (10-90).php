 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Http;

use Cake\Core\Configure;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Cookie\CookieInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\DateTime as CakeDateTime;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use Laminas\Diactoros\MessageTrait;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SplFileInfo;
use Stringable;
use function Cake\Core\env;
use function Cake\I18n\__d;

/**
 * Responses contain the response text, status and headers of a HTTP response.
 *
 * There are external packages such as `fig/http-message-util` that provide HTTP
 * status code constants. These can be used with any method that accepts or
 * returns a status code integer. Keep in mind that these constants might
 * include status codes that are not allowed which will throw an
 * `\InvalidArgumentException`.
 */
class Response implements ResponseInterface, Stringable
{
    use MessageTrait;

    /**
     * @var int
     */
    public const STATUS_CODE_MIN = 100;

    /**
     * @var int
     */
    public const STATUS_CODE_MAX = 599;

    /**
     * Allowed HTTP status codes and their default description.
     *
     * @var array<int, string>
     */
    protected array $_statusCodes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        226 => 'IM used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',