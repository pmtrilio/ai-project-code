/**
 * Controller is the base class of console command classes.
 *
 * A console controller consists of one or several actions known as sub-commands.
 * Users call a console command by specifying the corresponding route which identifies a controller action.
 * The `yii` program is used when calling a console command, like the following:
 *
 * ```
 * yii <route> [--param1=value1 --param2 ...]
 * ```
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