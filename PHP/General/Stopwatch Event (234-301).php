    public function __toString(): string
    {
        return \sprintf('%s/%s: %.2F MiB - %d ms', $this->getCategory(), $this->getName(), $this->getMemory() / 1024 / 1024, $this->getDuration());
    }
}
