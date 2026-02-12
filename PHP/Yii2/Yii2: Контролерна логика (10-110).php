
use Yii;
use yii\di\Instance;
use yii\di\NotInstantiableException;

/**
 * Controller is the base class for classes containing controller logic.
 *
 * For more details and usage information on Controller, see the [guide article on controllers](guide:structure-controllers).
 *
 * @property-read Module[] $modules All ancestor modules that this controller is located within.
 * @property-read string $route The route (module ID, controller ID and action ID) of the current request.
 * @property-read string $uniqueId The controller ID that is prefixed with the module ID (if any).
 * @property View|\yii\web\View $view The view object that can be used to render views or view files.
 * @property string $viewPath The directory containing the view files for this controller.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 *
 * @template T of Module
 */
class Controller extends Component implements ViewContextInterface
{
    /**
     * @event ActionEvent an event raised right before executing a controller action.
     * You may set [[ActionEvent::isValid]] to be false to cancel the action execution.
     */
    public const EVENT_BEFORE_ACTION = 'beforeAction';
    /**
     * @event ActionEvent an event raised right after executing a controller action.
     */
    public const EVENT_AFTER_ACTION = 'afterAction';
    /**
     * @var string the ID of this controller.
     */
    public $id;
    /**
     * @var T the module that this controller belongs to.
     */
    public $module;
    /**
     * @var string the ID of the action that is used when the action ID is not specified
     * in the request. Defaults to 'index'.
     */
    public $defaultAction = 'index';
    /**
     * @var string|null|false the name of the layout to be applied to this controller's views.
     * This property mainly affects the behavior of [[render()]].
     * Defaults to null, meaning the actual layout value should inherit that from [[module]]'s layout value.
     * If false, no layout will be applied.
     */
    public $layout;
    /**
     * @var Action<$this>|null the action that is currently being executed. This property will be set
     * by [[run()]] when it is called by [[Application]] to run an action.
     *
     * @phpstan-var Action<$this>|null
     * @psalm-var Action<self>|null
     */
    public $action;
    /**
     * @var Request|array|string The request.
     * @since 2.0.36
     */
    public $request = 'request';
    /**
     * @var Response|array|string The response.
     * @since 2.0.36
     */
    public $response = 'response';

    /**
     * @var View|null the view object that can be used to render views or view files.
     */
    private $_view;
    /**
     * @var string|null the root directory that contains view files for this controller.
     */
    private $_viewPath;


    /**
     * @param string $id the ID of this controller.
     * @param T $module the module that this controller belongs to.
     * @param array<string, mixed> $config name-value pairs that will be used to initialize the object properties.
     */
    public function __construct($id, $module, $config = [])
    {
        $this->id = $id;
        $this->module = $module;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * @since 2.0.36
     */
    public function init()
    {
        parent::init();
        $this->request = Instance::ensure($this->request, Request::className());