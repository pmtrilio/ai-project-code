        if (null !== $constraint->normalizer) {
            $value = ($constraint->normalizer)($value);
        }

        if (null === $constraint->mode) {
            if (Email::VALIDATION_MODE_STRICT === $this->defaultMode && !class_exists(EguliasEmailValidator::class)) {
                throw new LogicException(\sprintf('The "egulias/email-validator" component is required to make the "%s" constraint default to strict mode. Try running "composer require egulias/email-validator".', Email::class));
            }

            $constraint->mode = $this->defaultMode;
        }

        if (!\in_array($constraint->mode, Email::VALIDATION_MODES, true)) {
            throw new InvalidArgumentException(\sprintf('The "%s::$mode" parameter value is not valid.', get_debug_type($constraint)));
        }

        if (Email::VALIDATION_MODE_STRICT === $constraint->mode) {
            $strictValidator = new EguliasEmailValidator();

            if (interface_exists(EmailValidation::class) && !$strictValidator->isValid($value, new NoRFCWarningsValidation())) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(Email::INVALID_FORMAT_ERROR)
                    ->addViolation();
            } elseif (!interface_exists(EmailValidation::class) && !$strictValidator->isValid($value, false, true)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode(Email::INVALID_FORMAT_ERROR)
                    ->addViolation();
            }
        } elseif (!preg_match(self::EMAIL_PATTERNS[$constraint->mode], $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(Email::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
