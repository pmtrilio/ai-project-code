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
    /// Gets or sets the value for the <c>Content-Length</c> response header.
    /// </summary>
    public abstract long? ContentLength { get; set; }

    /// <summary>
    /// Gets or sets the value for the <c>Content-Type</c> response header.
    /// </summary>
    public abstract string? ContentType { get; set; }

    /// <summary>
    /// Gets an object that can be used to manage cookies for this response.
    /// </summary>
    public abstract IResponseCookies Cookies { get; }

    /// <summary>
    /// Gets a value indicating whether response headers have been sent to the client.
    /// </summary>
    public abstract bool HasStarted { get; }

    /// <summary>
    /// Adds a delegate to be invoked just before response headers will be sent to the client.
    /// Callbacks registered here run in reverse order.
    /// </summary>
    /// <remarks>
    /// Callbacks registered here run in reverse order. The last one registered is invoked first.
    /// The reverse order is done to replicate the way middleware works, with the inner-most middleware looking at the
    /// response first.
    /// </remarks>
    /// <param name="callback">The delegate to execute.</param>
    /// <param name="state">A state object to capture and pass back to the delegate.</param>
    public abstract void OnStarting(Func<object, Task> callback, object state);

    /// <summary>
    /// Adds a delegate to be invoked just before response headers will be sent to the client.
    /// Callbacks registered here run in reverse order.
    /// </summary>
    /// <remarks>
    /// Callbacks registered here run in reverse order. The last one registered is invoked first.
    /// The reverse order is done to replicate the way middleware works, with the inner-most middleware looking at the
    /// response first.
    /// </remarks>
    /// <param name="callback">The delegate to execute.</param>
    public virtual void OnStarting(Func<Task> callback) => OnStarting(_callbackDelegate, callback);

    /// <summary>
    /// Adds a delegate to be invoked after the response has finished being sent to the client.
    /// </summary>
    /// <param name="callback">The delegate to invoke.</param>
    /// <param name="state">A state object to capture and pass back to the delegate.</param>
    public abstract void OnCompleted(Func<object, Task> callback, object state);

    /// <summary>
    /// Registers an object for disposal by the host once the request has finished processing.
    /// </summary>
    /// <param name="disposable">The object to be disposed.</param>
    public virtual void RegisterForDispose(IDisposable disposable) => OnCompleted(_disposeDelegate, disposable);

    /// <summary>
    /// Registers an object for asynchronous disposal by the host once the request has finished processing.
    /// </summary>
    /// <param name="disposable">The object to be disposed asynchronously.</param>
    public virtual void RegisterForDisposeAsync(IAsyncDisposable disposable) => OnCompleted(_disposeDelegate, disposable);

    /// <summary>
    /// Adds a delegate to be invoked after the response has finished being sent to the client.
    /// </summary>
    /// <param name="callback">The delegate to invoke.</param>
    public virtual void OnCompleted(Func<Task> callback) => OnCompleted(_callbackDelegate, callback);

    /// <summary>