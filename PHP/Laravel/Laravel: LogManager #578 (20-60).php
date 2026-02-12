use Monolog\Logger as Monolog;
use Monolog\Processor\ProcessorInterface;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @mixin \Illuminate\Log\Logger
 */
class LogManager implements LoggerInterface
{
    use ParsesLogConfiguration;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The context shared across channels and stacks.
     *
     * @var array
     */
    protected $sharedContext = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];
