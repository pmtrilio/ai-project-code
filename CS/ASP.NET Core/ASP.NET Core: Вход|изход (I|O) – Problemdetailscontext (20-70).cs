    /// <summary>
    /// A collection of additional arbitrary metadata associated with the current request endpoint.
    /// </summary>
    public EndpointMetadataCollection? AdditionalMetadata { get; init; }

    /// <summary>
    /// An instance of <see cref="ProblemDetails"/> that will be
    /// used during the response payload generation.
    /// </summary>
    public ProblemDetails ProblemDetails
    {
        get => _problemDetails ??= new ProblemDetails();
        set => _problemDetails = value;
    }

    /// <summary>
    /// The exception causing the problem or <c>null</c> if no exception information is available.
    /// </summary>
    public Exception? Exception { get; init; }
}
