        $currentLabel = $options['label'] ?? '';

        if (!$withMetadata) {
            // Only currentLabel to handle. If null, will be translated to empty string
            return \sprintf('"%s"', $this->escape($currentLabel));
        }
        $workflowMetadata = $definition->getMetadataStore()->getWorkflowMetadata();

        if ('' === $currentLabel) {
            // Only metadata to handle
            return \sprintf('<%s>', $this->addMetadata($workflowMetadata, false));
        }

        // currentLabel and metadata to handle
        return \sprintf('<<B>%s</B>%s>', $this->escape($currentLabel), $this->addMetadata($workflowMetadata));
    }

    private function addOptions(array $options): string
    {
        $code = [];

        foreach ($options as $k => $v) {
            $code[] = \sprintf('%s="%s"', $k, $v);
        }

        return implode(' ', $code);
    }

    /**
     * @param bool $lineBreakFirstIfNotEmpty Whether to add a separator in the first place when metadata is not empty
     */
    private function addMetadata(array $metadata, bool $lineBreakFirstIfNotEmpty = true): string
    {
        $code = [];

        $skipSeparator = !$lineBreakFirstIfNotEmpty;

        foreach ($metadata as $key => $value) {
            if ($skipSeparator) {
                $code[] = \sprintf('%s: %s', $this->escape($key), $this->escape($value));
                $skipSeparator = false;
            } else {
                $code[] = \sprintf('%s%s: %s', '<BR/>', $this->escape($key), $this->escape($value));
            }
        }

        return $code ? implode('', $code) : '';
    }
}
