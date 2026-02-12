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