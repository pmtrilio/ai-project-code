    {
        $contentType = $request->getHeader('Content-Type')[0] ?? null;

        if (is_string($contentType) && trim($contentType) !== '') {
            $contentTypeParts = explode(';', $contentType);
            return strtolower(trim($contentTypeParts[0]));
        }

        return null;
    }

    protected static function disableXmlEntityLoader(bool $disable): bool
    {
        if (LIBXML_VERSION >= 20900) {
            // libxml >= 2.9.0 disables entity loading by default, so it is
            // safe to skip the real call (deprecated in PHP 8).
            return true;
        }

        // @codeCoverageIgnoreStart
        return libxml_disable_entity_loader($disable);
        // @codeCoverageIgnoreEnd
    }
}
