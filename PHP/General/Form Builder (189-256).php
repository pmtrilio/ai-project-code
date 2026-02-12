    /**
     * Converts an unresolved child into a {@link FormBuilderInterface} instance.
     */
    private function resolveChild(string $name): FormBuilderInterface
    {
        [$type, $options] = $this->unresolvedChildren[$name];

        unset($this->unresolvedChildren[$name]);

        return $this->children[$name] = $this->create($name, $type, $options);
    }

    /**
     * Converts all unresolved children into {@link FormBuilder} instances.
     */
    private function resolveChildren(): void
    {
        foreach ($this->unresolvedChildren as $name => $info) {
            $this->children[$name] = $this->create($name, $info[0], $info[1]);
        }

        $this->unresolvedChildren = [];
    }
}
