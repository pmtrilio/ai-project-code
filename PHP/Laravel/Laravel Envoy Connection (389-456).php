     *
     * @param  string  $macro
     * @param  array  $options
     * @return void
     */
    public function startMacro($macro, array $options = [])
    {
        ob_start() && $this->macroStack[] = $macro;

        $this->macroOptions[$macro] = $options;
    }

    /**
     * Stop defining a macro.
     *
     * @return void
     */
    public function endMacro()
    {
        $macro = array_map('trim', preg_split('/\n|\r\n?/', $this->trimSpaces(trim(ob_get_clean()))));

        $this->macros[array_pop($this->macroStack)] = $macro;
    }

    /**
     * Begin defining a task.
     *
     * @param  string  $task
     * @param  array  $options
     * @return void
     */
    public function startTask($task, array $options = [])
    {
        ob_start() && $this->taskStack[] = $task;

        $this->taskOptions[$task] = $this->mergeDefaultOptions($options);
    }

    /**
     * Merge the option array over the default options.
     *
     * @param  array  $options
     * @return array
     */
    protected function mergeDefaultOptions(array $options)
    {
        return array_merge(['as' => null, 'on' => array_keys($this->servers)], $options);
    }

    /**
     * Stop defining a task.
     *
     * @return void
     */
    public function endTask()
    {
        $name = array_pop($this->taskStack);

        $contents = trim(ob_get_clean());

        if (isset($this->tasks[$name])) {
            $this->tasks[$name] = str_replace('@parent', $this->tasks[$name], $contents);
        } else {
            $this->tasks[$name] = $contents;
        }
    }

    /**