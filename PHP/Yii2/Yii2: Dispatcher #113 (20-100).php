 *
 * An instance of Dispatcher is registered as a core application component and can be accessed using `Yii::$app->log`.
 *
 * You may configure the targets in application configuration, like the following:
 *
 * ```
 * [
 *     'components' => [
 *         'log' => [
 *             'targets' => [
 *                 'file' => [
 *                     'class' => 'yii\log\FileTarget',
 *                     'levels' => ['trace', 'info'],
 *                     'categories' => ['yii\*'],
 *                 ],
 *                 'email' => [
 *                     'class' => 'yii\log\EmailTarget',
 *                     'levels' => ['error', 'warning'],
 *                     'message' => [
 *                         'to' => 'admin@example.com',
 *                     ],
 *                 ],
 *             ],
 *         ],
 *     ],
 * ]
 * ```
 *
 * Each log target can have a name and can be referenced via the [[targets]] property as follows:
 *
 * ```
 * Yii::$app->log->targets['file']->enabled = false;
 * ```
 *
 * @property int $flushInterval How many messages should be logged before they are sent to targets. See
 * [[getFlushInterval()]] and [[setFlushInterval()]] for details.
 * @property Logger $logger The logger. If not set, [[Yii::getLogger()]] will be used. Note that the type of
 * this property differs in getter and setter. See [[getLogger()]] and [[setLogger()]] for details.
 * @property int $traceLevel How many application call stacks should be logged together with each message.
 * This method returns the value of [[Logger::traceLevel]]. Defaults to 0.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Dispatcher extends Component
{
    /**
     * @var array|Target[] the log targets. Each array element represents a single [[Target|log target]] instance
     * or the configuration for creating the log target instance.
     */
    public $targets = [];

    /**
     * @var Logger|null the logger.
     */
    private $_logger;


    /**
     * {@inheritdoc}
     */
    public function __construct($config = [])
    {
        // ensure logger gets set before any other config option
        if (isset($config['logger'])) {
            $this->setLogger($config['logger']);
            unset($config['logger']);
        }
        // connect logger and dispatcher
        $this->getLogger();

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
