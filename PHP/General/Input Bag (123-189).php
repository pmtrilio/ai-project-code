    public function filter(string $key, mixed $default = null, int $filter = \FILTER_DEFAULT, mixed $options = []): mixed
    {
        $value = $this->has($key) ? $this->all()[$key] : $default;

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!\is_array($options) && $options) {
            $options = ['flags' => $options];
        }

        if (\is_array($value) && !(($options['flags'] ?? 0) & (\FILTER_REQUIRE_ARRAY | \FILTER_FORCE_ARRAY))) {
            throw new BadRequestException(\sprintf('Input value "%s" contains an array, but "FILTER_REQUIRE_ARRAY" or "FILTER_FORCE_ARRAY" flags were not set.', $key));
        }

        if ((\FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof \Closure)) {
            throw new \InvalidArgumentException(\sprintf('A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.', __METHOD__, get_debug_type($options['options'] ?? null)));
        }

        $options['flags'] ??= 0;
        $nullOnFailure = $options['flags'] & \FILTER_NULL_ON_FAILURE;
        $options['flags'] |= \FILTER_NULL_ON_FAILURE;

        $value = filter_var($value, $filter, $options);

        if (null !== $value || $nullOnFailure) {
            return $value;
        }

        throw new BadRequestException(\sprintf('Input value "%s" is invalid and flag "FILTER_NULL_ON_FAILURE" was not set.', $key));
    }
}
