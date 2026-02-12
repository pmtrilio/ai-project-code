 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\base;

use ReflectionClass;
use Yii;

/**
 * Widget is the base class for widgets.
 *
 * For more details and usage information on Widget, see the [guide article on widgets](guide:structure-widgets).
 *
 * @property string|null $id ID of the widget. Note that the type of this property differs in getter and
 * setter. See [[getId()]] and [[setId()]] for details.
 * @property \yii\web\View $view The view object that can be used to render views or view files. Note that the
 * type of this property differs in getter and setter. See [[getView()]] and [[setView()]] for details.
 * @property-read string $viewPath The directory containing the view files for this widget.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Widget extends Component implements ViewContextInterface
{
    /**
     * @event Event an event that is triggered when the widget is initialized via [[init()]].
     * @since 2.0.11
     */
    public const EVENT_INIT = 'init';
    /**
     * @event WidgetEvent an event raised right before executing a widget.
     * You may set [[WidgetEvent::isValid]] to be false to cancel the widget execution.
     * @since 2.0.11
     */
    public const EVENT_BEFORE_RUN = 'beforeRun';
    /**
     * @event WidgetEvent an event raised right after executing a widget.
     * @since 2.0.11
     */
    public const EVENT_AFTER_RUN = 'afterRun';
    /**
     * @var int a counter used to generate [[id]] for widgets.
     * @internal
     */
    public static $counter = 0;
    /**
     * @var string the prefix to the automatically generated widget IDs.
     * @see getId()
     */
    public static $autoIdPrefix = 'w';
    /**
     * @var Widget[] the widgets that are currently being rendered (not ended). This property
     * is maintained by [[begin()]] and [[end()]] methods.
     * @internal
     */
    public static $stack = [];

    /**
     * @var string[] used widget classes that have been resolved to their actual class name.
     */