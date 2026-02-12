    private static readonly Func<object, Task> _disposeDelegate = state =>
    {
        // Prefer async dispose over dispose
        if (state is IAsyncDisposable asyncDisposable)
        {
            return asyncDisposable.DisposeAsync().AsTask();
        }
        else if (state is IDisposable disposable)
        {
            disposable.Dispose();
        }
        return Task.CompletedTask;
    };

    /// <summary>
    /// Gets the <see cref="HttpContext"/> for this response.
    /// </summary>
    public abstract HttpContext HttpContext { get; }

    /// <summary>
    /// Gets or sets the HTTP response code.
    /// </summary>
    public abstract int StatusCode { get; set; }

    /// <summary>
    /// Gets the response headers.
    /// </summary>
    public abstract IHeaderDictionary Headers { get; }

    /// <summary>
    /// Gets or sets the response body <see cref="Stream"/>.
    /// </summary>
    public abstract Stream Body { get; set; }

    /// <summary>
    /// Gets the response body <see cref="PipeWriter"/>
    /// </summary>
    /// <value>The response body <see cref="PipeWriter"/>.</value>
    public virtual PipeWriter BodyWriter { get => throw new NotImplementedException(); }

    /// <summary>