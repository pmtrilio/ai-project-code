use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Queue\Events\JobPopped;
use Illuminate\Queue\Events\JobPopping;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobReleasedAfterException;
use Illuminate\Queue\Events\JobTimedOut;
use Illuminate\Queue\Events\Looping;
use Illuminate\Queue\Events\WorkerStarting;
use Illuminate\Queue\Events\WorkerStopping;
use Illuminate\Support\Carbon;
use Throwable;

class Worker
{
    use DetectsLostConnections;

    const EXIT_SUCCESS = 0;
    const EXIT_ERROR = 1;
    const EXIT_MEMORY_LIMIT = 12;

    /**
     * The name of the worker.
     *
     * @var string|null
     */
    protected $name;

    /**
     * The queue manager instance.
     *
     * @var \Illuminate\Contracts\Queue\Factory
     */
    protected $manager;

    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The cache repository implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The exception handler instance.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $exceptions;

    /**
     * The callback used to determine if the application is in maintenance mode.
     *
     * @var callable