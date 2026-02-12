     *
     * @return iterable<TabularDataReader>
     */
    public function chunkBy(int $recordsCount): iterable
    {
        return ResultSet::from($this)->chunkBy($recordsCount);
    }

    /**
     * @param array<string> $headers
     */
    public function mapHeader(array $headers): TabularDataReader
    {
        return (new Statement())->process($this, $headers);
    }

    /**
     * @param \League\Csv\Query\Predicate|Closure(array, array-key): bool $predicate
     *
     * @throws Exception
     * @throws SyntaxError
     */
    public function filter(Query\Predicate|Closure $predicate): TabularDataReader
    {
        return (new Statement())->where($predicate)->process($this);
    }

    /**
     * @param int<0, max> $offset
     * @param int<-1, max> $length
     *
     * @throws Exception
     * @throws SyntaxError
     */
    public function slice(int $offset, int $length = -1): TabularDataReader
    {
        return (new Statement())->offset($offset)->limit($length)->process($this);
    }

    /**
     * @param Closure(mixed, mixed): int $orderBy
     *
     * @throws Exception
     * @throws SyntaxError
     */
    public function sorted(Query\Sort|Closure $orderBy): TabularDataReader
    {
        return (new Statement())->orderBy($orderBy)->process($this);
    }

    /**
     * EXPERIMENTAL WARNING! This method implementation will change in the next major point release.
     *
     * Extract all found fragment identifiers for the specifield tabular data
     *
     * @experimental since version 9.12.0
     *
     * @throws SyntaxError
     * @return iterable<int, TabularDataReader>
     */
    public function matching(string $expression): iterable
    {
        return (new FragmentFinder())->findAll($expression, $this);
    }

    /**
     * EXPERIMENTAL WARNING! This method implementation will change in the next major point release.