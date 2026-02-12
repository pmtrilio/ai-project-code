 *
 * where `<route>` is a route to a controller action and the params will be populated as properties of a command.
 * See [[options()]] for details.
 *
 * @property Request $request The request object.
 * @property Response $response The response object.
 * @property-read string $help The help information for this controller.
 * @property-read string $helpSummary The one-line short summary describing this controller.
 * @property-read array $passedOptionValues The properties corresponding to the passed options.
 * @property-read array $passedOptions The names of the options passed during execution.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 *
 * @template T of Module
 * @extends BaseController<T>
 */
class Controller extends BaseController
{
    /**
     * @deprecated since 2.0.13. Use [[ExitCode::OK]] instead.
     */
    public const EXIT_CODE_NORMAL = 0;
    /**
     * @deprecated since 2.0.13. Use [[ExitCode::UNSPECIFIED_ERROR]] instead.
     */
    public const EXIT_CODE_ERROR = 1;
    /**
     * @var bool whether to run the command interactively.
     */
    public $interactive = true;
    /**
     * @var bool|null whether to enable ANSI color in the output.
     * If not set, ANSI color will only be enabled for terminals that support it.
     */
    public $color;
    /**
     * @var bool whether to display help information about current command.
     * @since 2.0.10
     */
    public $help = false;
    /**
     * @var bool|null if true - script finish with `ExitCode::OK` in case of exception.
     * false - `ExitCode::UNSPECIFIED_ERROR`.
     * Default: `YII_ENV_TEST`
     * @since 2.0.36
     */
    public $silentExitOnException;

    /**
     * @var array the options passed during execution.
     */
    private $_passedOptions = [];


    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $silentExit = $this->silentExitOnException !== null ? $this->silentExitOnException : YII_ENV_TEST;
        Yii::$app->errorHandler->silentExitOnException = $silentExit;

        return parent::beforeAction($action);
    }

    /**
     * Returns a value indicating whether ANSI color is enabled.
     *
     * ANSI color is enabled only if [[color]] is set true or is not set
     * and the terminal supports ANSI color.
     *
     * @param resource $stream the stream to check.
     * @return bool Whether to enable ANSI style in output.
     */
    public function isColorEnabled($stream = \STDOUT)
    {
        return $this->color === null ? Console::streamSupportsAnsiColors($stream) : $this->color;
    }

    /**
     * Runs an action with the specified action ID and parameters.
     * If the action ID is empty, the method will use [[defaultAction]].
     * @param string $id the ID of the action to be executed.
     * @param array $params the parameters (name-value pairs) to be passed to the action.
     * @return int the status of the action execution. 0 means normal, other values mean abnormal.
     * @throws InvalidRouteException if the requested action ID cannot be resolved into an action successfully.
     * @throws Exception if there are unknown options or missing arguments
     * @see createAction
     */
    public function runAction($id, $params = [])
    {
        if (!empty($params)) {
            // populate options here so that they are available in beforeAction().
            $options = $this->options($id === '' ? $this->defaultAction : $id);
            if (isset($params['_aliases'])) {
                $optionAliases = $this->optionAliases();
                foreach ($params['_aliases'] as $name => $value) {
                    if (array_key_exists($name, $optionAliases)) {
                        $params[$optionAliases[$name]] = $value;
                    } else {