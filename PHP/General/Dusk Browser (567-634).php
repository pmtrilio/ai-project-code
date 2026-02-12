            $this->driver, new ElementResolver($this->driver, $this->resolver->format($selector))
        );

        if ($this->page) {
            $browser->onWithoutAssert($this->page);
        }

        if ($selector instanceof Component) {
            $browser->onComponent($selector, $this->resolver);
        }

        call_user_func($callback, $browser);

        return $this;
    }

    /**
     * Execute a Closure outside of the current browser scope.
     *
     * @param  string|\Laravel\Dusk\Component  $selector
     * @param  \Closure  $callback
     * @return $this
     */
    public function elsewhere($selector, Closure $callback)
    {
        $browser = new static(
            $this->driver, new ElementResolver($this->driver, 'body '.$selector)
        );

        if ($this->page) {
            $browser->onWithoutAssert($this->page);
        }

        if ($selector instanceof Component) {
            $browser->onComponent($selector, $this->resolver);
        }

        call_user_func($callback, $browser);

        return $this;
    }

    /**
     * Execute a Closure outside of the current browser scope when the selector is available.
     *
     * @param  string  $selector
     * @param  \Closure  $callback
     * @param  int|null  $seconds
     * @return $this
     */
    public function elsewhereWhenAvailable($selector, Closure $callback, $seconds = null)
    {
        return $this->elsewhere('', function ($browser) use ($selector, $callback, $seconds) {
            $browser->whenAvailable($selector, $callback, $seconds);
        });
    }

    /**
     * Return a browser scoped to the given component.
     *
     * @param  \Laravel\Dusk\Component  $component
     * @return \Laravel\Dusk\Browser
     */
    public function component(Component $component)
    {
        $browser = new static(
            $this->driver, new ElementResolver($this->driver, $this->resolver->format($component))
        );