    private ?CompiledRoute $compiled = null;

    /**
     * Constructor.
     *
     * Available options:
     *
     *  * compiler_class: A class name able to compile this route instance (RouteCompiler by default)
     *  * utf8:           Whether UTF-8 matching is enforced or not
     *
     * @param string                    $path         The path pattern to match
     * @param array                     $defaults     An array of default parameter values
     * @param array<string|\Stringable> $requirements An array of requirements for parameters (regexes)
     * @param array                     $options      An array of options
     * @param string|null               $host         The host pattern to match
     * @param string|string[]           $schemes      A required URI scheme or an array of restricted schemes
     * @param string|string[]           $methods      A required HTTP method or an array of restricted methods
     * @param string|null               $condition    A condition that should evaluate to true for the route to match
     */
    public function __construct(string $path, array $defaults = [], array $requirements = [], array $options = [], ?string $host = '', string|array $schemes = [], string|array $methods = [], ?string $condition = '')
    {
        $this->setPath($path);
        $this->addDefaults($defaults);
        $this->addRequirements($requirements);
        $this->setOptions($options);
        $this->setHost($host);
        $this->setSchemes($schemes);
        $this->setMethods($methods);
        $this->setCondition($condition);
    }

    public function __serialize(): array
    {
        return [
            'path' => $this->path,
            'host' => $this->host,
            'defaults' => $this->defaults,
            'requirements' => $this->requirements,
            'options' => $this->options,
            'schemes' => $this->schemes,
            'methods' => $this->methods,