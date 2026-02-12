     * @param  string  $line
     * @param  array  $replace
     * @return string
     */
    protected function makeReplacements($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }

        $shouldReplace = [];

        foreach ($replace as $key => $value) {
            if ($value instanceof Closure) {
                $line = preg_replace_callback(
                    '/<'.$key.'>(.*?)<\/'.$key.'>/',
                    fn ($args) => $value($args[1]),
                    $line
                );

                continue;
            }

            if (is_object($value)) {
                $value = isset($this->stringableHandlers[get_class($value)])
                    ? call_user_func($this->stringableHandlers[get_class($value)], $value)
                    : enum_value($value);
            }

            $shouldReplace[':'.Str::ucfirst($key)] = Str::ucfirst($value ?? '');
            $shouldReplace[':'.Str::upper($key)] = Str::upper($value ?? '');
            $shouldReplace[':'.$key] = $value;
        }

        return strtr($line, $shouldReplace);
    }

    /**
     * Add translation lines to the given locale.
     *
     * @param  array  $lines
     * @param  string  $locale
     * @param  string  $namespace
     * @return void
     */
    public function addLines(array $lines, $locale, $namespace = '*')
    {
        foreach ($lines as $key => $value) {
            [$group, $item] = explode('.', $key, 2);

            Arr::set($this->loaded, "$namespace.$group.$locale.$item", $value);
        }
    }

    /**
     * Load the specified language group.
     *