 * @since 2.0
 */
class ErrorHandler extends \yii\base\ErrorHandler
{
    /**
     * @var int maximum number of source code lines to be displayed. Defaults to 19.
     */
    public $maxSourceLines = 19;
    /**
     * @var int maximum number of trace source code lines to be displayed. Defaults to 13.
     */
    public $maxTraceSourceLines = 13;
    /**
     * @var string|null the route (e.g. `site/error`) to the controller action that will be used
     * to display external errors. Inside the action, it can retrieve the error information
     * using `Yii::$app->errorHandler->exception`. This property defaults to null, meaning ErrorHandler
     * will handle the error display.
     */
    public $errorAction;
    /**
     * @var string the path of the view file for rendering exceptions without call stack information.
     */
    public $errorView = '@yii/views/errorHandler/error.php';
    /**
     * @var string the path of the view file for rendering exceptions.
     */
    public $exceptionView = '@yii/views/errorHandler/exception.php';
    /**
     * @var string the path of the view file for rendering exceptions and errors call stack element.
     */
    public $callStackItemView = '@yii/views/errorHandler/callStackItem.php';
    /**
     * @var string the path of the view file for rendering previous exceptions.
     */
    public $previousExceptionView = '@yii/views/errorHandler/previousException.php';
    /**
     * @var array list of the PHP predefined variables that should be displayed on the error page.
     * Note that a variable must be accessible via `$GLOBALS`. Otherwise it won't be displayed.
     * Defaults to `['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION']`.
     * @see renderRequest()
     * @since 2.0.7
     */
    public $displayVars = ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'];
    /**
     * @var string trace line with placeholders to be be substituted.
     * The placeholders are {file}, {line} and {text} and the string should be as follows.
     *
     * `File: {file} - Line: {line} - Text: {text}`
     *
     * @example <a href="ide://open?file={file}&line={line}">{html}</a>
     * @see https://github.com/yiisoft/yii2-debug#open-files-in-ide
     * @since 2.0.14
     */
    public $traceLine = '{html}';


    /**
     * Renders the exception.
     * @param \Throwable $exception the exception to be rendered.
     */
    protected function renderException($exception)
    {
        if (Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            // reset parameters of response to avoid interference with partially created response data
            // in case the error occurred while sending the response.
            $response->isSent = false;
            $response->stream = null;
            $response->data = null;
            $response->content = null;
        } else {
            $response = new Response();
        }

        $response->setStatusCodeByException($exception);

        $useErrorView = $response->format === Response::FORMAT_HTML && (!YII_DEBUG || $exception instanceof UserException);

        if ($useErrorView && $this->errorAction !== null) {
            /** @var View */
            $view = Yii::$app->view;