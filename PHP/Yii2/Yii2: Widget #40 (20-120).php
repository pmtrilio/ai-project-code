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
    private static $_resolvedClasses = [];


    /**
     * Initializes the object.
     * This method is called at the end of the constructor.
     * The default implementation will trigger an [[EVENT_INIT]] event.
     */
    public function init()
    {
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

    /**
     * Begins a widget.
     * This method creates an instance of the calling class. It will apply the configuration
     * to the created instance. A matching [[end()]] call should be called later.
     * As some widgets may use output buffering, the [[end()]] call should be made in the same view
     * to avoid breaking the nesting of output buffers.
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @return static the newly created widget instance
     * @see end()
     */
    public static function begin($config = [])
    {
        $config['class'] = get_called_class();
        /** @var static $widget */
        $widget = Yii::createObject($config);
        self::$stack[] = $widget;
        self::$_resolvedClasses[get_called_class()] = get_class($widget);

        return $widget;
    }

    /**
     * Ends a widget.
     * Note that the rendering result of the widget is directly echoed out.
     * @return static the widget instance that is ended.
     * @throws InvalidCallException if [[begin()]] and [[end()]] calls are not properly nested
     * @see begin()
     */
    public static function end()
    {
        if (!empty(self::$stack)) {
            $widget = array_pop(self::$stack);

            $calledClass = self::$_resolvedClasses[get_called_class()] ?? get_called_class();

            if (get_class($widget) === $calledClass) {
                /** @var static $widget */
                if ($widget->beforeRun()) {
                    $result = $widget->run();
                    $result = $widget->afterRun($result);
                    echo $result;