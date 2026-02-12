    /// <param name="viewEngine">The <see cref="IRazorViewEngine"/> used to locate Layout pages.</param>
    /// <param name="pageActivator">The <see cref="IRazorPageActivator"/> used to activate pages.</param>
    /// <param name="viewStartPages">The sequence of <see cref="IRazorPage" /> instances executed as _ViewStarts.
    /// </param>
    /// <param name="razorPage">The <see cref="IRazorPage"/> instance to execute.</param>
    /// <param name="htmlEncoder">The HTML encoder.</param>
    /// <param name="diagnosticListener">The <see cref="DiagnosticListener"/>.</param>
    public RazorView(
        IRazorViewEngine viewEngine,
        IRazorPageActivator pageActivator,
        IReadOnlyList<IRazorPage> viewStartPages,
        IRazorPage razorPage,
        HtmlEncoder htmlEncoder,
        DiagnosticListener diagnosticListener)
    {
        ArgumentNullException.ThrowIfNull(viewEngine);
        ArgumentNullException.ThrowIfNull(pageActivator);
        ArgumentNullException.ThrowIfNull(viewStartPages);
        ArgumentNullException.ThrowIfNull(razorPage);
        ArgumentNullException.ThrowIfNull(htmlEncoder);
        ArgumentNullException.ThrowIfNull(diagnosticListener);

        _viewEngine = viewEngine;
        _pageActivator = pageActivator;
        ViewStartPages = viewStartPages;
        RazorPage = razorPage;
        _htmlEncoder = htmlEncoder;
        _diagnosticListener = diagnosticListener;
    }

    /// <inheritdoc />
    public string Path => RazorPage.Path;

    /// <summary>
    /// Gets <see cref="IRazorPage"/> instance that the views executes on.
    /// </summary>
    public IRazorPage RazorPage { get; }

    /// <summary>
    /// Gets the sequence of _ViewStart <see cref="IRazorPage"/> instances that are executed by this view.
    /// </summary>
    public IReadOnlyList<IRazorPage> ViewStartPages { get; }

    internal Action<IRazorPage, ViewContext>? OnAfterPageActivated { get; set; }

    /// <inheritdoc />
    public virtual async Task RenderAsync(ViewContext context)
    {
        ArgumentNullException.ThrowIfNull(context);

        // This GetRequiredService call is by design. ViewBufferScope is a scoped service, RazorViewEngine
        // is the component responsible for creating RazorViews and it is a Singleton service. It doesn't
        // have access to the RequestServices so requiring the service when we render the page is the best
        // we can do.
        _bufferScope = context.HttpContext.RequestServices.GetRequiredService<IViewBufferScope>();
        var bodyWriter = await RenderPageAsync(RazorPage, context, invokeViewStarts: true);
        await RenderLayoutAsync(context, bodyWriter);
    }

    private async Task<ViewBufferTextWriter> RenderPageAsync(
        IRazorPage page,