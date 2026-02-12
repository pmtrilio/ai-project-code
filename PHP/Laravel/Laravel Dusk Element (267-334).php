     * Resolve the element for a given button by name.
     *
     * @param  string  $button
     * @return \Facebook\WebDriver\Remote\RemoteWebElement|null
     */
    protected function findButtonByName($button)
    {
        if (! is_null($element = $this->find("input[type=submit][name='{$button}']")) ||
            ! is_null($element = $this->find("input[type=button][value='{$button}']")) ||
            ! is_null($element = $this->find("button[name='{$button}']"))) {
            return $element;
        }
    }

    /**
     * Resolve the element for a given button by value.
     *
     * @param  string  $button
     * @return \Facebook\WebDriver\Remote\RemoteWebElement|null
     */
    protected function findButtonByValue($button)
    {
        foreach ($this->all('input[type=submit]') as $element) {
            if ($element->getAttribute('value') === $button) {
                return $element;
            }
        }
    }

    /**
     * Resolve the element for a given button by text.
     *
     * @param  string  $button
     * @return \Facebook\WebDriver\Remote\RemoteWebElement|null
     */
    protected function findButtonByText($button)
    {
        foreach ($this->all('button') as $element) {
            if (Str::contains($element->getText(), $button)) {
                return $element;
            }
        }
    }

    /**
     * Attempt to find the selector by ID.
     *
     * @param  string  $selector
     * @return \Facebook\WebDriver\Remote\RemoteWebElement|null
     */
    protected function findById($selector)
    {
        if (preg_match('/^#[\w\-:]+$/', $selector)) {
            return $this->driver->findElement(WebDriverBy::id(substr($selector, 1)));
        }
    }

    /**
     * Find an element by the given selector or return null.
     *
     * @param  string  $selector
     * @return \Facebook\WebDriver\Remote\RemoteWebElement|null
     */
    public function find($selector)
    {
        try {
            return $this->findOrFail($selector);
        } catch (Exception $e) {