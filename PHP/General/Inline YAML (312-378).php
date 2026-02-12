            // a non-quoted string cannot start with @ or ` (reserved) nor with a scalar indicator (| or >)
            if ($output && ('@' === $output[0] || '`' === $output[0] || '|' === $output[0] || '>' === $output[0] || '%' === $output[0])) {
                throw new ParseException(\sprintf('The reserved indicator "%s" cannot start a plain scalar; you need to quote the scalar.', $output[0]), self::$parsedLineNumber + 1, $output, self::$parsedFilename);
            }

            if ($evaluate) {
                $output = self::evaluateScalar($output, $flags, $references, $isQuoted);
            }
        }

        return $output;
    }

    /**
     * Parses a YAML quoted scalar.
     *
     * @throws ParseException When malformed inline YAML string is parsed
     */
    private static function parseQuotedScalar(string $scalar, int &$i = 0): string
    {
        if (!Parser::preg_match('/'.self::REGEX_QUOTED_STRING.'/Au', substr($scalar, $i), $match)) {
            throw new ParseException(\sprintf('Malformed inline YAML string: "%s".', substr($scalar, $i)), self::$parsedLineNumber + 1, $scalar, self::$parsedFilename);
        }

        $output = substr($match[0], 1, -1);

        $unescaper = new Unescaper();
        if ('"' == $scalar[$i]) {
            $output = $unescaper->unescapeDoubleQuotedString($output);
        } else {
            $output = $unescaper->unescapeSingleQuotedString($output);
        }

        $i += \strlen($match[0]);

        return $output;
    }

    /**
     * Parses a YAML sequence.
     *
     * @throws ParseException When malformed inline YAML string is parsed
     */
    private static function parseSequence(string $sequence, int $flags, int &$i = 0, array &$references = []): array
    {
        $output = [];
        $len = \strlen($sequence);
        ++$i;

        // [foo, bar, ...]
        $lastToken = null;
        while ($i < $len) {
            if (']' === $sequence[$i]) {
                return $output;
            }
            if (',' === $sequence[$i] || ' ' === $sequence[$i]) {
                if (',' === $sequence[$i] && (null === $lastToken || 'separator' === $lastToken)) {
                    $output[] = null;
                } elseif (',' === $sequence[$i]) {
                    $lastToken = 'separator';
                }

                ++$i;

                continue;
            }
