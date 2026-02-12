 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;
use yii\helpers\FileHelper;
use yii\widgets\Block;
use yii\widgets\ContentDecorator;
use yii\widgets\FragmentCache;

/**
 * View represents a view object in the MVC pattern.
 *
 * View provides a set of methods (e.g. [[render()]]) for rendering purpose.
 *
 * For more details and usage information on View, see the [guide article on views](guide:structure-views).
 *
 * @property-read DynamicContentAwareInterface[] $dynamicContents Class instances supporting dynamic contents.
 * @property-read string|bool $viewFile The view file currently being rendered. False if no view file is being
 * rendered.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
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
     */
    public $renderers;
    /**
     * @var string the default view file extension. This will be appended to view file names if they don't have file extensions.
     */
    public $defaultExtension = 'php';
    /**
     * @var Theme|array|string|null the theme object or the configuration for creating the theme object.
     * If not set, it means theming is not enabled.
     */
    public $theme;
    /**
     * @var array a list of named output blocks. The keys are the block names and the values
     * are the corresponding block content. You can call [[beginBlock()]] and [[endBlock()]]
     * to capture small fragments of a view. They can be later accessed somewhere else
     * through this property.
     */
    public $blocks;
    /**
     * @var array|DynamicContentAwareInterface[] a list of currently active dynamic content class instances.
     * This property is used internally to implement the dynamic content caching feature. Do not modify it directly.
     * @internal
     * @deprecated Since 2.0.14. Do not use this property directly. Use methods [[getDynamicContents()]],
     * [[pushDynamicContent()]], [[popDynamicContent()]] instead.
     */
    public $cacheStack = [];
    /**
     * @var array a list of placeholders for embedding dynamic contents. This property
     * is used internally to implement the content caching feature. Do not modify it directly.
     * @internal
     * @deprecated Since 2.0.14. Do not use this property directly. Use methods [[getDynamicPlaceholders()]],
     * [[setDynamicPlaceholders()]], [[addDynamicPlaceholder()]] instead.
     */
    public $dynamicPlaceholders = [];

    /**
     * @var array the view files currently being rendered. There may be multiple view files being
     * rendered at a moment because one view may be rendered within another.
     */
    private $_viewFiles = [];


    /**
     * Initializes the view component.
     */
    public function init()
    {
        parent::init();
        if (is_array($this->theme)) {
            if (!isset($this->theme['class'])) {
                $this->theme['class'] = 'yii\base\Theme';
            }
            $this->theme = Yii::createObject($this->theme);
        } elseif (is_string($this->theme)) {
            $this->theme = Yii::createObject($this->theme);