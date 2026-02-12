        return head($this->servers);
    }

    /**
     * Import the given file into the container.
     *
     * @param  string  $file
     * @param  array  $data
     * @return void
     */
    public function import($file, array $data = [])
    {
        $data = Arr::except($data, [
            '__path', '__dir', '__compiler', '__data', '__serversOnly',
            '__envoyPath', '__container', 'this',
        ]);

        if (($path = $this->resolveImportPath($file)) === false) {
            throw new InvalidArgumentException("Unable to locate file: [{$file}].");
        }

        $this->load($path, new Compiler, $data);
    }

    /**
     * Resolve the import path for the given file.
     *
     * @param  string  $file
     * @return string|bool
     */
    protected function resolveImportPath($file)
    {
        if (($path = realpath($file)) !== false) {
            return $path;
        } elseif (($path = realpath($file.'.blade.php')) !== false) {
            return $path;
        } elseif (($path = realpath(getcwd().'/vendor/'.$file.'/Envoy.blade.php')) !== false) {
            return $path;
        } elseif (($path = realpath(__DIR__.'/'.$file.'.blade.php')) !== false) {
            return $path;
        }

        return false;
    }

    /**
     * Share the given piece of data across all tasks.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function share($key, $value)
    {
        $this->sharedData[$key] = $value;
    }

    /**
     * Getter for macros.
     *
     * @return array
     */
    public function getMacros()
    {
        return $this->macros;
    }

    /**