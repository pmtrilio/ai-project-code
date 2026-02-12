    /// If the response body exceeds this limit, it will not be cached by the <see cref="ResponseCachingMiddleware"/>.
    /// </summary>
    public long MaximumBodySize { get; set; } = 64 * 1024 * 1024;

    /// <summary>
    /// <c>true</c> if request paths are case-sensitive; otherwise <c>false</c>. The default is to treat paths as case-insensitive.
    /// </summary>
    public bool UseCaseSensitivePaths { get; set; }

    /// <summary>
    /// For testing purposes only.
    /// </summary>
    internal TimeProvider TimeProvider { get; set; } = TimeProvider.System;
}
