                        $hasTitle ? $this->headerTitle : null,
                        $hasTitle ? $this->style->getHeaderTitleFormat() : null
                    );
                    $hasTitle = false;
                    $isHeaderSeparatorRendered = true;
                }

                if ($isFirstRow) {
                    $this->renderRowSeparator(
                        $horizontal ? self::SEPARATOR_TOP : self::SEPARATOR_TOP_BOTTOM,
                        $hasTitle ? $this->headerTitle : null,
                        $hasTitle ? $this->style->getHeaderTitleFormat() : null
                    );
                    $isFirstRow = false;
                    $hasTitle = false;
                }

                if ($vertical) {
                    $isHeader = false;
                    $isFirstRow = false;
                }

                if ($horizontal) {
                    $this->renderRow($row, $this->style->getCellRowFormat(), $this->style->getCellHeaderFormat());
                } else {
                    $this->renderRow($row, $isHeader ? $this->style->getCellHeaderFormat() : $this->style->getCellRowFormat());
                }
            }
        }

        if ($this->getStyle()->displayOutsideBorder()) {
            $this->renderRowSeparator(self::SEPARATOR_BOTTOM, $this->footerTitle, $this->style->getFooterTitleFormat());
        }

        $this->cleanup();
        $this->rendered = true;
    }

    /**
     * Renders horizontal header separator.
     *
     * Example:
     *
     *     +-----+-----------+-------+
     */
    private function renderRowSeparator(int $type = self::SEPARATOR_MID, ?string $title = null, ?string $titleFormat = null): void
    {
        if (!$count = $this->numberOfColumns) {
            return;
        }

        $borders = $this->style->getBorderChars();
        if (!$borders[0] && !$borders[2] && !$this->style->getCrossingChar()) {
            return;
        }

        $crossings = $this->style->getCrossingChars();
        if (self::SEPARATOR_MID === $type) {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[2], $crossings[8], $crossings[0], $crossings[4]];
        } elseif (self::SEPARATOR_TOP === $type) {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[0], $crossings[1], $crossings[2], $crossings[3]];
        } elseif (self::SEPARATOR_TOP_BOTTOM === $type) {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[0], $crossings[9], $crossings[10], $crossings[11]];
        } else {
            [$horizontal, $leftChar, $midChar, $rightChar] = [$borders[0], $crossings[7], $crossings[6], $crossings[5]];
        }
