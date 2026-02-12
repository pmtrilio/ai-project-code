 * other application specific logic. The events are not implemented by this base class, as
 * implementing a callback method subscribes a helper to the related event. The callback methods
 * are as follows:
 *
 * - `beforeRender(EventInterface $event, $viewFile)` - beforeRender is called before the view file is rendered.
 * - `afterRender(EventInterface $event, $viewFile)` - afterRender is called after the view file is rendered
 *   but before the layout has been rendered.
 * - beforeLayout(EventInterface $event, $layoutFile)` - beforeLayout is called before the layout is rendered.
 * - `afterLayout(EventInterface $event, $layoutFile)` - afterLayout is called after the layout has rendered.
 * - `beforeRenderFile(EventInterface $event, $viewFile)` - Called before any view fragment is rendered.
 * - `afterRenderFile(EventInterface $event, $viewFile, $content)` - Called after any view fragment is rendered.
 *   If a listener returns a non-null value, the output of the rendered file will be set to that.
 */
class Helper implements EventListenerInterface
{
    use InstanceConfigTrait;

    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    protected array $helpers = [];

    /**
     * Default config for this helper.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [];

    /**
     * Loaded helper instances.
     *
     * @var array<string, \Cake\View\Helper>
     */
    protected array $helperInstances = [];

    /**
     * The View instance this helper is attached to
     *
     * @var \Cake\View\View
     */
    protected View $_View;

    /**
     * Default Constructor
     *
     * @param \Cake\View\View $view The View this helper is being attached to.
     * @param array<string, mixed> $config Configuration settings for the helper.
     */
    public function __construct(View $view, array $config = [])
    {
        $this->_View = $view;
        $this->setConfig($config);

        if ($this->helpers) {
            $this->helpers = $view->helpers()->normalizeArray($this->helpers);
        }

        $this->initialize($config);