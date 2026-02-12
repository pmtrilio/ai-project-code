
class View implements ArrayAccess, Htmlable, Stringable, ViewContract
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * The view factory instance.
     *
     * @var \Illuminate\View\Factory
     */
    protected $factory;

    /**
     * The engine implementation.
     *
     * @var \Illuminate\Contracts\View\Engine
     */
    protected $engine;

    /**
     * The name of the view.
     *
     * @var string
     */
    protected $view;

    /**
     * The array of view data.
     *
     * @var array
     */
    protected $data;

    /**
     * The path to the view file.
     *
     * @var string
     */
    protected $path;