use Laminas\EventManager\Event;
use Laminas\Router\RouteMatch;
use Laminas\Router\RouteStackInterface;
use Laminas\Stdlib\RequestInterface as Request;
use Laminas\Stdlib\ResponseInterface as Response;
use Laminas\View\Model\ModelInterface as Model;
use Laminas\View\Model\ViewModel;

class MvcEvent extends Event
{
    /**#@+
     * Mvc events triggered by eventmanager
     */
    public const EVENT_BOOTSTRAP      = 'bootstrap';
    public const EVENT_DISPATCH       = 'dispatch';
    public const EVENT_DISPATCH_ERROR = 'dispatch.error';
    public const EVENT_FINISH         = 'finish';
    public const EVENT_RENDER         = 'render';
    public const EVENT_RENDER_ERROR   = 'render.error';
    public const EVENT_ROUTE          = 'route';
    /** @var ApplicationInterface|null */
    protected $application;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var mixed */
    protected $result;

    /** @var RouteStackInterface */
    protected $router;

    /** @var null|RouteMatch */
    protected $routeMatch;

    /** @var Model */
    protected $viewModel;

    /**
     * Set application instance
     *
     * @return MvcEvent
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->setParam('application', $application);
        $this->application = $application;
        return $this;
    }

    /**
     * Get application instance
     *
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->application;