        $result = preg_replace_callback('/'.$this->getRawPlaceholder('(\d+)').'/', function ($matches) {
            return $this->rawBlocks[$matches[1]];
        }, $result);

        $this->rawBlocks = [];

        return $result;
    }

    /**
     * Get a placeholder to temporarily mark the position of raw blocks.
     *
     * @param  int|string  $replace
     * @return string
     */
    protected function getRawPlaceholder($replace)
    {
        return str_replace('#', $replace, '@__raw_block_#__@');
    }

    /**
     * Add the stored footers onto the given content.
     *
     * @param  string  $result
     * @return string
     */
    protected function addFooters($result)
    {
        return ltrim($result, "\n")
                ."\n".implode("\n", array_reverse($this->footer));
    }

    /**
     * Parse the tokens from the template.
     *
     * @param  array  $token
     * @return string
     */
    protected function parseToken($token)
    {
        [$id, $content] = $token;

        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }

        return $content;
    }

    /**
     * Execute the user defined extensions.
     *
     * @param  string  $value
     * @return string
     */
    protected function compileExtensions($value)
    {
        foreach ($this->extensions as $compiler) {
            $value = $compiler($value, $this);
        }

        return $value;
    }

    /**
     * Compile Blade statements that start with "@".