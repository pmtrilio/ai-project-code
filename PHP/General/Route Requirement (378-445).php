    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @return $this
     */
    public function setCondition(?string $condition): static
    {
        $this->condition = (string) $condition;
        $this->compiled = null;

        return $this;
    }

    /**
     * Compiles the route.
     *
     * @throws \LogicException If the Route cannot be compiled because the
     *                         path or host pattern is invalid
     *
     * @see RouteCompiler which is responsible for the compilation process
     */
    public function compile(): CompiledRoute
    {
        if (null !== $this->compiled) {
            return $this->compiled;
        }

        $class = $this->getOption('compiler_class');

        return $this->compiled = $class::compile($this);
    }

    private function extractInlineDefaultsAndRequirements(string $pattern): string
    {
        if (false === strpbrk($pattern, '?<:')) {
            return $pattern;
        }

        $mapping = $this->getDefault('_route_mapping') ?? [];

        $pattern = preg_replace_callback('#\{(!?)([\w\x80-\xFF]++)(:([\w\x80-\xFF]++)(\.[\w\x80-\xFF]++)?)?(<.*?>)?(\?[^\}]*+)?\}#', function ($m) use (&$mapping) {
            if (isset($m[7][0])) {
                $this->setDefault($m[2], '?' !== $m[7] ? substr($m[7], 1) : null);
            }
            if (isset($m[6][0])) {
                $this->setRequirement($m[2], substr($m[6], 1, -1));
            }
            if (isset($m[4][0])) {
                $mapping[$m[2]] = isset($m[5][0]) ? [$m[4], substr($m[5], 1)] : $m[4];
            }

            return '{'.$m[1].$m[2].'}';
        }, $pattern);

        if ($mapping) {
            $this->setDefault('_route_mapping', $mapping);
        }

        return $pattern;
    }

    private function sanitizeRequirement(string $key, string $regex): string
    {
        if ('' !== $regex) {
            if ('^' === $regex[0]) {