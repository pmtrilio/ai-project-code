    {
        $mainCls = $cls;
        if (null !== $index) {
            $cls .= '___'.$index;
        }

        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false)) {
            $key = $this->cache->generateKey($name, $mainCls);

            if (!$this->isAutoReload() || $this->isTemplateFresh($name, $this->cache->getTimestamp($key))) {
                $this->cache->load($key);
            }

            if (!class_exists($cls, false)) {
                $source = $this->getLoader()->getSourceContext($name);
                $content = $this->compileSource($source);
                if (!isset($this->hotCache[$name])) {
                    $this->cache->write($key, $content);
                    $this->cache->load($key);
                }

                if (!class_exists($mainCls, false)) {
                    /* Last line of defense if either $this->bcWriteCacheFile was used,
                     * $this->cache is implemented as a no-op or we have a race condition
                     * where the cache was cleared between the above calls to write to and load from
                     * the cache.
                     */
                    eval('?>'.$content);
                }

                if (!class_exists($cls, false)) {
                    throw new RuntimeError(\sprintf('Failed to load Twig template "%s", index "%s": cache might be corrupted.', $name, $index), -1, $source);
                }
            }
        }

        $this->extensionSet->initRuntime();

        return $this->loadedTemplates[$cls] = new $cls($this);
    }

    /**
     * Creates a template from source.
     *
     * This method should not be used as a generic way to load templates.
     *
     * @param string      $template The template source
     * @param string|null $name     An optional name of the template to be used in error messages
     *
     * @throws LoaderError When the template cannot be found
     * @throws SyntaxError When an error occurred during compilation
     */
    public function createTemplate(string $template, ?string $name = null): TemplateWrapper
    {
        $hash = hash(\PHP_VERSION_ID < 80100 ? 'sha256' : 'xxh128', $template, false);
        if (null !== $name) {
            $name = \sprintf('%s (string template %s)', $name, $hash);
        } else {
            $name = \sprintf('__string_template__%s', $hash);
        }

        $loader = new ChainLoader([
            new ArrayLoader([$name => $template]),
            $current = $this->getLoader(),