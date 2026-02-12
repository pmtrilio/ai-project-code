use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\View\ViewException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CompilerEngine extends PhpEngine
{
    /**
     * The Blade compiler instance.
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    protected $compiler;

    /**
     * A stack of the last compiled templates.
     *
     * @var array
     */
    protected $lastCompiled = [];

    /**
     * The view paths that were compiled or are not expired, keyed by the path.
     *
     * @var array<string, true>
     */
    protected $compiledOrNotExpired = [];

    /**
     * Create a new compiler engine instance.
     *
     * @param  \Illuminate\View\Compilers\CompilerInterface  $compiler
     * @param  \Illuminate\Filesystem\Filesystem|null  $files
     */
    public function __construct(CompilerInterface $compiler, ?Filesystem $files = null)
    {
        parent::__construct($files ?: new Filesystem);

        $this->compiler = $compiler;
    }
