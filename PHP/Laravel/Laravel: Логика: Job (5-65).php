use Illuminate\Bus\Batchable;
use Illuminate\Bus\BatchRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\ManuallyFailedException;
use Illuminate\Queue\TimeoutExceededException;
use Illuminate\Support\InteractsWithTime;
use Throwable;

abstract class Job
{
    use InteractsWithTime;

    /**
     * The job handler instance.
     *
     * @var mixed
     */
    protected $instance;

    /**
     * The IoC container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Indicates if the job has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Indicates if the job has been released.
     *
     * @var bool
     */
    protected $released = false;

    /**
     * Indicates if the job has failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * The name of the connection the job belongs to.
     *
     * @var string
     */
    protected $connectionName;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string
     */
    protected $queue;