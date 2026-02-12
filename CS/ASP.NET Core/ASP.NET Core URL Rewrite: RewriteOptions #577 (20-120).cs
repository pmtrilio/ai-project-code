    /// <summary>
    /// Gets and sets the File Provider for file and directory checks.
    /// </summary>
    /// <value>
    /// Defaults to <see cref="IHostingEnvironment.WebRootFileProvider"/>.
    /// </value>
    public IFileProvider StaticFileProvider { get; set; } = default!;

    internal RequestDelegate? BranchedNext { get; set; }
}
