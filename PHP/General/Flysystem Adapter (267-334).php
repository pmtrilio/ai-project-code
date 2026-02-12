    private function rewindStream($resource): void
    {
        if (ftell($resource) !== 0 && stream_get_meta_data($resource)['seekable']) {
            rewind($resource);
        }
    }

    private function resolveConfigForMoveAndCopy(array $config): Config
    {
        $retainVisibility = $this->config->get(Config::OPTION_RETAIN_VISIBILITY, $config[Config::OPTION_RETAIN_VISIBILITY] ?? true);
        $fullConfig = $this->config->extend($config);

        /*
         * By default, we retain visibility. When we do not retain visibility, the visibility setting
         * from the default configuration is ignored. Only when it is set explicitly, we propagate the
         * setting.
         */
        if ($retainVisibility && ! array_key_exists(Config::OPTION_VISIBILITY, $config)) {
            $fullConfig = $fullConfig->withoutSettings(Config::OPTION_VISIBILITY)->extend($config);
        }

        return $fullConfig;
    }
}
