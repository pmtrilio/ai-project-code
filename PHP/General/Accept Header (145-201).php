        return $this->items ? reset($this->items) : null;
    }

    /**
     * Sorts items by descending quality.
     */
    private function sort(): void
    {
        if (!$this->sorted) {
            uasort($this->items, static fn ($a, $b) => $b->getQuality() <=> $a->getQuality() ?: $a->getIndex() <=> $b->getIndex());

            $this->sorted = true;
        }
    }

    /**
     * Generates the canonical key for storing/retrieving an item.
     */
    private function getCanonicalKey(AcceptHeaderItem $item): string
    {
        $parts = [];

        // Normalize and sort attributes for consistent key generation
        $attributes = $this->getMediaParams($item);
        ksort($attributes);

        foreach ($attributes as $name => $value) {
            if (null === $value) {
                $parts[] = $name; // Flag parameter (e.g., "flowed")
                continue;
            }

            // Quote values containing spaces, commas, semicolons, or equals per RFC 9110
            // This handles cases like 'format="value with space"' or similar.
            $quotedValue = \is_string($value) && preg_match('/[\s;,=]/', $value) ? '"'.addcslashes($value, '"\\').'"' : $value;

            $parts[] = $name.'='.$quotedValue;
        }

        return $item->getValue().($parts ? ';'.implode(';', $parts) : '');
    }

    /**
     * Checks if a given header item (range) matches a queried item (value).
     *
     * @param AcceptHeaderItem $rangeItem The item from the Accept header (e.g., text/*;format=flowed)
     * @param AcceptHeaderItem $queryItem The item being queried (e.g., text/plain;format=flowed;charset=utf-8)
     */
    private function matches(AcceptHeaderItem $rangeItem, AcceptHeaderItem $queryItem): bool
    {
        $rangeValue = strtolower($rangeItem->getValue());
        $queryValue = strtolower($queryItem->getValue());

        // Handle universal wildcard ranges
        if ('*' === $rangeValue || '*/*' === $rangeValue) {
            return $this->rangeParametersMatch($rangeItem, $queryItem);
        }