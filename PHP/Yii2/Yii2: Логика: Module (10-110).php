
use Yii;
use yii\di\ServiceLocator;

/**
 * Module is the base class for module and application classes.
 *
 * A module represents a sub-application which contains MVC elements by itself, such as
 * models, views, controllers, etc.
 *
 * A module may consist of [[modules|sub-modules]].
 *
 * [[components|Components]] may be registered with the module so that they are globally
 * accessible within the module.
 *
 * For more details and usage information on Module, see the [guide article on modules](guide:structure-modules).
 *
 * @property-write array $aliases List of path aliases to be defined. The array keys are alias names (must
 * start with `@`) and the array values are the corresponding paths or aliases. See [[setAliases()]] for an
 * example.
 * @property string $basePath The root directory of the module.
 * @property string $controllerPath The directory that contains the controller classes.
 * @property string $layoutPath The root directory of layout files. Defaults to "[[viewPath]]/layouts".
 * @property array $modules The modules (indexed by their IDs).
 * @property-read string $uniqueId The unique ID of the module.
 * @property string $version The version of this module. Note that the type of this property differs in getter
 * and setter. See [[getVersion()]] and [[setVersion()]] for details.
 * @property string $viewPath The root directory of view files. Defaults to "[[basePath]]/views".
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Module extends ServiceLocator
{
    /**
     * @event ActionEvent an event raised before executing a controller action.
     * You may set [[ActionEvent::isValid]] to be `false` to cancel the action execution.
     */
    public const EVENT_BEFORE_ACTION = 'beforeAction';
    /**
     * @event ActionEvent an event raised after executing a controller action.
     */
    public const EVENT_AFTER_ACTION = 'afterAction';
    /**
     * @var array custom module parameters (name => value).
     */
    public $params = [];
    /**
     * @var string an ID that uniquely identifies this module among other modules which have the same [[module|parent]].
     */
    public $id;
    /**
     * @var Module|null the parent module of this module. `null` if this module does not have a parent.
     */
    public $module;
    /**
     * @var string|bool|null the layout that should be applied for views within this module. This refers to a view name
     * relative to [[layoutPath]]. If this is not set, it means the layout value of the [[module|parent module]]
     * will be taken. If this is `false`, layout will be disabled within this module.
     */
    public $layout;
    /**
     * @var array mapping from controller ID to controller configurations.
     * Each name-value pair specifies the configuration of a single controller.
     * A controller configuration can be either a string or an array.
     * If the former, the string should be the fully qualified class name of the controller.
     * If the latter, the array must contain a `class` element which specifies
     * the controller's fully qualified class name, and the rest of the name-value pairs
     * in the array are used to initialize the corresponding controller properties. For example,
     *
     * ```
     * [
     *   'account' => 'app\controllers\UserController',
     *   'article' => [
     *      'class' => 'app\controllers\PostController',
     *      'pageTitle' => 'something new',
     *   ],
     * ]
     * ```
     */
    public $controllerMap = [];
    /**
     * @var string|null the namespace that controller classes are in.
     * This namespace will be used to load controller classes by prepending it to the controller
     * class name.
     *
     * If not set, it will use the `controllers` sub-namespace under the namespace of this module.
     * For example, if the namespace of this module is `foo\bar`, then the default
     * controller namespace would be `foo\bar\controllers`.
     *
     * See also the [guide section on autoloading](guide:concept-autoloading) to learn more about
     * defining namespaces and how classes are loaded.
     */
    public $controllerNamespace;
    /**
     * @var string the default route of this module. Defaults to `default`.
     * The route may consist of child module ID, controller ID, and/or action ID.
     * For example, `help`, `post/create`, `admin/post/create`.
     * If action ID is not given, it will take the default value as specified in
     * [[Controller::defaultAction]].
     */