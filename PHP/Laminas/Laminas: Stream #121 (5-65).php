namespace Laminas\Log\Writer;

use Laminas\Log\Exception;
use Laminas\Log\Formatter\Simple as SimpleFormatter;
use Laminas\Stdlib\ErrorHandler;
use Traversable;

use function chmod;
use function dirname;
use function fclose;
use function file_exists;
use function fopen;
use function fwrite;
use function get_resource_type;
use function gettype;
use function is_array;
use function is_resource;
use function is_string;
use function is_writable;
use function iterator_to_array;
use function sprintf;
use function touch;

use const PHP_EOL;

class Stream extends AbstractWriter
{
    /**
     * Separator between log entries
     *
     * @var string
     */
    protected $logSeparator = PHP_EOL;

    /**
     * Holds the PHP stream to log to.
     *
     * @var null|Stream
     */
    protected $stream;

    /**
     * Constructor
     *
     * @param  string|resource|array|Traversable $streamOrUrl Stream or URL to open as a stream
     * @param  string|null $mode Mode, only applicable if a URL is given
     * @param  null|string $logSeparator Log separator string
     * @param  null|int $filePermissions Permissions value, only applicable if a filename is given;
     *     when $streamOrUrl is an array of options, use the 'chmod' key to specify this.
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function __construct($streamOrUrl, $mode = null, $logSeparator = null, $filePermissions = null)
    {
        if ($streamOrUrl instanceof Traversable) {
            $streamOrUrl = iterator_to_array($streamOrUrl);
        }

        if (is_array($streamOrUrl)) {
            parent::__construct($streamOrUrl);
            $mode            = $streamOrUrl['mode'] ?? null;