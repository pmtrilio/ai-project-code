use Closure;
use Illuminate\Contracts\Queue\Factory as FactoryContract;
use Illuminate\Contracts\Queue\Monitor as MonitorContract;
use Illuminate\Support\Queue\Concerns\ResolvesQueueRoutes;
use InvalidArgumentException;

/**
 * @mixin \Illuminate\Contracts\Queue\Queue
 */
class QueueManager implements FactoryContract, MonitorContract
{
    use ResolvesQueueRoutes;

    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The array of resolved queue connectors.
     *
     * @var array
     */
    protected $connectors = [];

    /**
     * Create a new queue manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct($app)
    {