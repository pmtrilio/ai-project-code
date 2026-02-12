    public function waitUntilVue($key, $value, $componentSelector = null, $seconds = null)
    {
        $this->waitUsing($seconds, 100, function () use ($key, $value, $componentSelector) {
            return $value == $this->vueAttribute($componentSelector, $key);
        });

        return $this;
    }

    /**
     * Wait until the Vue component's attribute at the given key does not have the given value.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string|null  $componentSelector
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitUntilVueIsNot($key, $value, $componentSelector = null, $seconds = null)
    {
        $this->waitUsing($seconds, 100, function () use ($key, $value, $componentSelector) {
            return $value != $this->vueAttribute($componentSelector, $key);
        });

        return $this;
    }

    /**
     * Wait for a JavaScript dialog to open.
     *
     * @param  int|null  $seconds
     * @return $this
     */
    public function waitForDialog($seconds = null)
    {
        $seconds = is_null($seconds) ? static::$waitSeconds : $seconds;

        $this->driver->wait($seconds, 100)->until(
            WebDriverExpectedCondition::alertIsPresent(), "Waited {$seconds} seconds for dialog."
        );

        return $this;
    }

    /**
     * Wait for the current page to reload.
     *
     * @param  \Closure|null  $callback
     * @param  int|null  $seconds
     * @return $this
     *
     * @throws \Facebook\WebDriver\Exception\TimeoutException
     */
    public function waitForReload($callback = null, $seconds = null)
    {
        $token = Str::random();

        $this->driver->executeScript("window['{$token}'] = {};");

        if ($callback) {
            $callback($this);
        }

        return $this->waitUsing($seconds, 100, function () use ($token) {
            return $this->driver->executeScript("return typeof window['{$token}'] === 'undefined';");
        }, 'Waited %s seconds for page reload.');
    }
