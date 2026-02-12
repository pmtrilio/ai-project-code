                $this->setDefault($m[2], '?' !== $m[7] ? substr($m[7], 1) : null);
            }
            if (isset($m[6][0])) {
                $this->setRequirement($m[2], substr($m[6], 1, -1));
            }
            if (isset($m[4][0])) {
                $mapping[$m[2]] = isset($m[5][0]) ? [$m[4], substr($m[5], 1)] : $m[4];
            }

            return '{'.$m[1].$m[2].'}';
        }, $pattern);

        if ($mapping) {
            $this->setDefault('_route_mapping', $mapping);
        }

        return $pattern;
    }

    private function sanitizeRequirement(string $key, string $regex): string
    {
        if ('' !== $regex) {
            if ('^' === $regex[0]) {
                $regex = substr($regex, 1);
            } elseif (str_starts_with($regex, '\\A')) {
                $regex = substr($regex, 2);
            }
        }

        if (str_ends_with($regex, '$')) {
            $regex = substr($regex, 0, -1);
        } elseif (\strlen($regex) - 2 === strpos($regex, '\\z')) {
            $regex = substr($regex, 0, -2);
        }

        if ('' === $regex) {
            throw new \InvalidArgumentException(\sprintf('Routing requirement for "%s" cannot be empty.', $key));
        }

        return $regex;
    }

    private function isLocalized(): bool
    {
        return isset($this->defaults['_locale']) && isset($this->defaults['_canonical_route']) && ($this->requirements['_locale'] ?? null) === preg_quote($this->defaults['_locale']);
    }
}
