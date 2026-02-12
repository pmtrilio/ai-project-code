    public function configureOptions(OptionsResolver $resolver): void
    {
        $compound = static fn (Options $options) => 'single_text' !== $options['widget'];

        $placeholderDefault = static fn (Options $options) => $options['required'] ? null : '';

        $placeholderNormalizer = static function (Options $options, $placeholder) use ($placeholderDefault) {
            if (\is_array($placeholder)) {
                $default = $placeholderDefault($options);

                return array_merge(
                    ['year' => $default, 'month' => $default, 'day' => $default],
                    $placeholder
                );
            }

            return [
                'year' => $placeholder,
                'month' => $placeholder,
                'day' => $placeholder,
            ];
        };

        $choiceTranslationDomainNormalizer = static function (Options $options, $choiceTranslationDomain) {
            if (\is_array($choiceTranslationDomain)) {
                return array_replace(
                    ['year' => false, 'month' => false, 'day' => false],
                    $choiceTranslationDomain
                );
            }

            return [
                'year' => $choiceTranslationDomain,
                'month' => $choiceTranslationDomain,
                'day' => $choiceTranslationDomain,
            ];
        };

        $format = static fn (Options $options) => 'single_text' === $options['widget'] ? self::HTML5_FORMAT : self::DEFAULT_FORMAT;

        $resolver->setDefaults([
            'years' => range((int) date('Y') - 5, (int) date('Y') + 5),
            'months' => range(1, 12),
            'days' => range(1, 31),
            'widget' => 'single_text',
            'input' => 'datetime',
            'format' => $format,
            'model_timezone' => null,
            'view_timezone' => null,
            'calendar' => null,
            'placeholder' => $placeholderDefault,
            'html5' => true,
            // Don't modify \DateTime classes by reference, we treat
            // them like immutable value objects
            'by_reference' => false,
            'error_bubbling' => false,
            // If initialized with a \DateTime object, FormType initializes
            // this option to "\DateTime". Since the internal, normalized
            // representation is not \DateTime, but an array, we need to unset
            // this option.
            'data_class' => null,
            'compound' => $compound,
            'empty_data' => static fn (Options $options) => $options['compound'] ? [] : '',
            'choice_translation_domain' => false,
            'input_format' => 'Y-m-d',
            'invalid_message' => 'Please enter a valid date.',
        ]);
