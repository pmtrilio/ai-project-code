
    public function getPriority(string $name): ?int
    {
        return $this->priorities[$name] ?? null;
    }
}
