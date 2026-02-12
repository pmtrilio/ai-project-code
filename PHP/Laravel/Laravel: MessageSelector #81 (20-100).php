
        if (($value = $this->extract($segments, $number)) !== null) {
            return trim($value);
        }

        $segments = $this->stripConditions($segments);

        $pluralIndex = $this->getPluralIndex($locale, $number);

        if (count($segments) === 1 || ! isset($segments[$pluralIndex])) {
            return $segments[0];
        }

        return $segments[$pluralIndex];
    }

    /**
     * Extract a translation string using inline conditions.
     *
     * @param  array  $segments
     * @param  int  $number
     * @return mixed
     */
    private function extract($segments, $number)
    {
        foreach ($segments as $part) {
            if (! is_null($line = $this->extractFromString($part, $number))) {
                return $line;
            }
        }
    }

    /**
     * Get the translation string if the condition matches.
     *
     * @param  string  $part
     * @param  int  $number
     * @return mixed
     */
    private function extractFromString($part, $number)
    {
        preg_match('/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s', $part, $matches);

        if (count($matches) !== 3) {
            return null;
        }

        $condition = $matches[1];

        $value = $matches[2];

        if (str_contains($condition, ',')) {
            [$from, $to] = explode(',', $condition, 2);

            if ($to === '*' && $number >= $from) {
                return $value;
            } elseif ($from === '*' && $number <= $to) {
                return $value;
            } elseif ($number >= $from && $number <= $to) {
                return $value;
            }
        }

        return $condition == $number ? $value : null;
    }

    /**
     * Strip the inline conditions from each segment, just leaving the text.
     *
     * @param  array  $segments
     * @return array
     */
    private function stripConditions($segments)
    {
        return (new Collection($segments))
            ->map(fn ($part) => preg_replace('/^[\{\[]([^\[\]\{\}]*)[\}\]]/', '', $part))
            ->all();
    }

    /**
     * Get the index to use for pluralization.