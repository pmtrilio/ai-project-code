    public function invalidate(?int $lifetime = null): bool
    {
        $this->storage->clear();

        return $this->migrate(true, $lifetime);
    }

    public function migrate(bool $destroy = false, ?int $lifetime = null): bool
    {
        return $this->storage->regenerate($destroy, $lifetime);
    }

    public function save(): void
    {
        $this->storage->save();
    }

    public function getId(): string
    {
        return $this->storage->getId();
    }

    public function setId(string $id): void
    {
        if ($this->storage->getId() !== $id) {
            $this->storage->setId($id);
        }
    }

    public function getName(): string
    {
        return $this->storage->getName();
    }

    public function setName(string $name): void
    {
        $this->storage->setName($name);
    }

    public function getMetadataBag(): MetadataBag
    {
        ++$this->usageIndex;
        if ($this->usageReporter && 0 <= $this->usageIndex) {
            ($this->usageReporter)();
        }