 */
class View extends Component implements DynamicContentAwareInterface
{
    /**
     * @event Event an event that is triggered by [[beginPage()]].
     */
    public const EVENT_BEGIN_PAGE = 'beginPage';
    /**
     * @event Event an event that is triggered by [[endPage()]].
     */
    public const EVENT_END_PAGE = 'endPage';
    /**
     * @event ViewEvent an event that is triggered by [[renderFile()]] right before it renders a view file.
     */
    public const EVENT_BEFORE_RENDER = 'beforeRender';
    /**
     * @event ViewEvent an event that is triggered by [[renderFile()]] right after it renders a view file.
     */
    public const EVENT_AFTER_RENDER = 'afterRender';
    /**
     * @var ViewContextInterface the context under which the [[renderFile()]] method is being invoked.
     */
    public $context;
    /**
     * @var array custom parameters that are shared among view templates.
     */
    public $params = [];
    /**
     * @var array|null a list of available renderers indexed by their corresponding supported file extensions.
     * Each renderer may be a view renderer object or the configuration for creating the renderer object.
     * For example, the following configuration enables both Smarty and Twig view renderers:
     *
     * ```
     * [
     *     'tpl' => ['class' => 'yii\smarty\ViewRenderer'],
     *     'twig' => ['class' => 'yii\twig\ViewRenderer'],
     * ]
     * ```
     *
     * If no renderer is available for the given view file, the view file will be treated as a normal PHP
     * and rendered via [[renderPhpFile()]].