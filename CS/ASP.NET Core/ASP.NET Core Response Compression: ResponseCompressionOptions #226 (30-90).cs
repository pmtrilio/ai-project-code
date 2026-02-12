    /// </remarks>
    public bool EnableForHttps { get; set; }

    /// <summary>
    /// The <see cref="ICompressionProvider"/> types to use for responses.
    /// Providers are prioritized based on the order they are added.
    /// </summary>
    public CompressionProviderCollection Providers { get; } = new CompressionProviderCollection();
}
