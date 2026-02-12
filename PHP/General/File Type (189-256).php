     * Converts the limit to the smallest possible number
     * (i.e. try "MB", then "kB", then "bytes").
     *
     * This method should be kept in sync with Symfony\Component\Validator\Constraints\FileValidator::factorizeSizes().
     */
    private function factorizeSizes(int $size, int|float $limit): array
    {
        $coef = self::MIB_BYTES;
        $coefFactor = self::KIB_BYTES;

        $limitAsString = (string) ($limit / $coef);

        // Restrict the limit to 2 decimals (without rounding! we
        // need the precise value)
        while (self::moreDecimalsThan($limitAsString, 2)) {
            $coef /= $coefFactor;
            $limitAsString = (string) ($limit / $coef);
        }

        // Convert size to the same measure, but round to 2 decimals
        $sizeAsString = (string) round($size / $coef, 2);

        // If the size and limit produce the same string output
        // (due to rounding), reduce the coefficient
        while ($sizeAsString === $limitAsString) {
            $coef /= $coefFactor;
            $limitAsString = (string) ($limit / $coef);
            $sizeAsString = (string) round($size / $coef, 2);
        }

        return [$limitAsString, self::SUFFIXES[$coef]];
    }

    /**
     * This method should be kept in sync with Symfony\Component\Validator\Constraints\FileValidator::moreDecimalsThan().
     */
    private static function moreDecimalsThan(string $double, int $numberOfDecimals): bool
    {
        return \strlen($double) > \strlen(round($double, $numberOfDecimals));
    }
}
