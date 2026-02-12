            // this option.
            'data_class' => null,
            'compound' => $compound,
            'date_label' => null,
            'time_label' => null,
            'empty_data' => static fn (Options $options) => $options['compound'] ? [] : '',
            'input_format' => 'Y-m-d H:i:s',
            'invalid_message' => 'Please enter a valid date and time.',
        ]);

        // Don't add some defaults in order to preserve the defaults
        // set in DateType and TimeType
        $resolver->setDefined([
            'placeholder',
            'choice_translation_domain',
            'years',
            'months',
            'days',
            'hours',
            'minutes',
            'seconds',
        ]);

        $resolver->setAllowedValues('input', [
            'datetime',
            'datetime_immutable',
            'date_point',
            'string',
            'timestamp',
            'array',
        ]);
        $resolver->setAllowedValues('date_widget', [
            null, // inherit default from DateType
            'single_text',
            'text',
            'choice',
        ]);
        $resolver->setAllowedValues('time_widget', [
            null, // inherit default from TimeType
            'single_text',
            'text',
            'choice',
        ]);
        // This option will overwrite "date_widget" and "time_widget" options
        $resolver->setAllowedValues('widget', [
            null, // default, don't overwrite options
            'single_text',
            'text',
            'choice',
        ]);

        $resolver->setAllowedTypes('input_format', 'string');

        $resolver->setNormalizer('date_format', static function (Options $options, $dateFormat) {
            if (null !== $dateFormat && 'single_text' === $options['widget'] && self::HTML5_FORMAT === $options['format']) {
                throw new LogicException(\sprintf('Cannot use the "date_format" option of the "%s" with an HTML5 date.', self::class));
            }

            return $dateFormat;
        });
        $resolver->setNormalizer('widget', static function (Options $options, $widget) {
            if ('single_text' === $widget) {
                if (null !== $options['date_widget']) {
                    throw new LogicException(\sprintf('Cannot use the "date_widget" option of the "%s" when the "widget" option is set to "single_text".', self::class));
                }
                if (null !== $options['time_widget']) {
                    throw new LogicException(\sprintf('Cannot use the "time_widget" option of the "%s" when the "widget" option is set to "single_text".', self::class));
                }