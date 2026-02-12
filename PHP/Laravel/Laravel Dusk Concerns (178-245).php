     *
     * @return $this
     */
    public function releaseMouse()
    {
        (new WebDriverActions($this->driver))->release()->perform();

        return $this;
    }
}
