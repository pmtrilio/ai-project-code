
        @unlink($__envoyPath);

        $this->replaceSubTasks();

        ob_end_clean();
    }

    /**
     * Write the compiled Envoy file to disk.
     *
     * @param  \Laravel\Envoy\Compiler  $compiler
     * @param  string  $path
     * @param  bool  $serversOnly
     * @return string
     */
    protected function writeCompiledEnvoyFile($compiler, $path, $serversOnly)
    {
        file_put_contents(
            $envoyPath = getcwd().'/Envoy'.md5_file($path).'.php',
            $compiler->compile(file_get_contents($path), $serversOnly)
        );

        return $envoyPath;
    }

    /**
     * Replace all of the sub tasks and trim leading spaces.
     *
     * @return void
     */
    protected function replaceSubTasks()
    {
        foreach ($this->tasks as $name => &$script) {
            $callback = function ($m) {
                return $m[1].$this->tasks[$m[2]];
            };

            $script = $this->trimSpaces(
                preg_replace_callback("/(\s*)@run\('(.*)'\)/", $callback, $script)
            );
        }
    }

    /**
     * Register the array of servers with the container.
     *
     * @param  array  $servers
     * @return void
     */
    public function servers(array $servers)
    {
        $this->servers = $servers;
    }

    /**
     * Get the IP address for a server.