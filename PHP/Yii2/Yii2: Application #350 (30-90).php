 * - User specifies which command to run on the command line;
 * - The command processes the user request with the specified parameters.
 *
 * The command classes should be under the namespace specified by [[controllerNamespace]].
 * Their naming should follow the same naming convention as controllers. For example, the `help` command
 * is implemented using the `HelpController` class.
 *
 * To run the console application, enter the following on the command line:
 *
 * ```
 * yii <route> [--param1=value1 --param2 ...]
 * ```
 *
 * where `<route>` refers to a controller route in the form of `ModuleID/ControllerID/ActionID`
 * (e.g. `sitemap/create`), and `param1`, `param2` refers to a set of named parameters that
 * will be used to initialize the controller action (e.g. `--since=0` specifies a `since` parameter
 * whose value is 0 and a corresponding `$since` parameter is passed to the action method).
 *
 * A `help` command is provided by default, which lists available commands and shows their usage.
 * To use this command, simply type:
 *
 * ```
 * yii help
 * ```
 *
 * @property-read ErrorHandler $errorHandler The error handler application component.
 * @property-read Request $request The request component.
 * @property-read Response $response The response component.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Application extends \yii\base\Application
{
    /**
     * The option name for specifying the application configuration file path.
     */
    public const OPTION_APPCONFIG = 'appconfig';
    /**
     * @var string the default route of this application. Defaults to 'help',
     * meaning the `help` command.
     */
    public $defaultRoute = 'help';
    /**
     * @var bool whether to enable the commands provided by the core framework.
     * Defaults to true.
     */
    public $enableCoreCommands = true;
    /**
     * @var Controller<Module>|null the currently active controller instance
     */
    public $controller;


    /**
     * {@inheritdoc}
     */
    public function __construct($config = [])
    {
        $config = $this->loadConfig($config);
        parent::__construct($config);