    public ILogger Logger { get; set; } = NullLogger.Instance;

    /// <summary>
    /// A shared result that is set appropriately by each rule for the next action that
    /// should be taken. See <see cref="RuleResult"/>
    /// </summary>
    public RuleResult Result { get; set; }

    internal StringBuilder Builder { get; set; } = new StringBuilder(64);
}
