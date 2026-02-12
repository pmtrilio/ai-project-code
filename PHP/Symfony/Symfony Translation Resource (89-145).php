    public function add(array $messages, string $domain = 'messages'): void;

    /**
     * Merges translations from the given Catalogue into the current one.
     *
     * The two catalogues must have the same locale.
     */
    public function addCatalogue(self $catalogue): void;

    /**
     * Merges translations from the given Catalogue into the current one
     * only when the translation does not exist.
     *
     * This is used to provide default translations when they do not exist for the current locale.
     */
    public function addFallbackCatalogue(self $catalogue): void;

    /**
     * Gets the fallback catalogue.
     */
    public function getFallbackCatalogue(): ?self;

    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return ResourceInterface[]
     */
    public function getResources(): array;

    /**
     * Adds a resource for this collection.
     */
    public function addResource(ResourceInterface $resource): void;
}
