     * Skip pagination as needed.
     *
     * @return $this
     */
    public function skipPaging(): static
    {
        $this->skipPaging = true;

        return $this;
    }

    /**
     * Skip auto filtering as needed.
     *
     * @return $this
     */
    public function skipAutoFilter(): static
    {
        $this->autoFilter = false;

        return $this;
    }

    /**
     * Push a new column name to blacklist.
     *
     * @param  string  $column
     * @return $this
     */
    public function pushToBlacklist($column): static
    {
        if (! $this->isBlacklisted($column)) {
            $this->columnDef['blacklist'][] = $column;
        }

        return $this;
    }

    /**
     * Check if column is blacklisted.
     *
     * @param  string  $column
     */
    protected function isBlacklisted($column): bool
    {
        $colDef = $this->getColumnsDefinition();

        if (in_array($column, $colDef['blacklist'])) {
            return true;
        }

        if ($colDef['whitelist'] === '*' || in_array($column, $colDef['whitelist'])) {
            return false;
        }

        return true;
    }

    /**
     * Perform sorting of columns.
     */
    public function ordering(): void
    {
        if ($this->orderCallback) {
            call_user_func_array($this->orderCallback, $this->resolveCallbackParameter());
        } else {
            $this->defaultOrdering();
        }