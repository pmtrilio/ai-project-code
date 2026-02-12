     */
    public static function instanceOfUnrelatedClass($className, $rootClass)
    {
        return new self("Cannot check if a child of '" . $rootClass . "' is instanceof '" . $className . "', " .
            'inheritance hierarchy does not exists between these two classes.');
    }

    /**
     * @param string $dqlAlias
     *
     * @return QueryException
     */
    public static function invalidQueryComponent($dqlAlias)
    {
        return new self(
            "Invalid query component given for DQL alias '" . $dqlAlias . "', " .
            "requires 'metadata', 'parent', 'relation', 'map', 'nestingLevel' and 'token' keys."
        );
    }
}
