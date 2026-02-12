                $childView->vars['full_name'] = $childName;
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $emptyData = static function (Options $options) {
            if ($options['expanded'] && !$options['multiple']) {
                return null;
            }

            if ($options['multiple']) {
                return [];
            }

            return '';
        };

        $placeholderDefault = static fn (Options $options) => $options['required'] ? null : '';

        $placeholderNormalizer = static function (Options $options, $placeholder) {
            if ($options['multiple']) {
                // never use an empty value for this case
                return null;
            } elseif ($options['required'] && ($options['expanded'] || isset($options['attr']['size']) && $options['attr']['size'] > 1)) {
                // placeholder for required radio buttons or a select with size > 1 does not make sense
                return null;
            } elseif (false === $placeholder) {
                // an empty value should be added but the user decided otherwise
                return null;
            } elseif ($options['expanded'] && '' === $placeholder) {
                // never use an empty label for radio buttons
                return 'None';
            }

            // empty value has been set explicitly
            return $placeholder;
        };

        $compound = static fn (Options $options) => $options['expanded'];

        $choiceTranslationDomainNormalizer = static function (Options $options, $choiceTranslationDomain) {
            if (true === $choiceTranslationDomain) {
                return $options['translation_domain'];
            }

            return $choiceTranslationDomain;
        };

        $choiceLoaderNormalizer = static function (Options $options, ?ChoiceLoaderInterface $choiceLoader) {
            if (!$options['choice_lazy']) {
                return $choiceLoader;
            }

            if (null === $choiceLoader) {
                throw new LogicException('The "choice_lazy" option can only be used if the "choice_loader" option is set.');
            }

            return new LazyChoiceLoader($choiceLoader);
        };

        $resolver->setDefaults([
            'multiple' => false,
            'expanded' => false,
            'choices' => [],
            'choice_filter' => null,
            'choice_lazy' => false,