{
    private readonly IRazorViewEngine _viewEngine;
    private readonly IRazorPageActivator _pageActivator;
    private readonly HtmlEncoder _htmlEncoder;
    private readonly DiagnosticListener _diagnosticListener;
    private IViewBufferScope? _bufferScope;

    /// <summary>
    /// Initializes a new instance of <see cref="RazorView"/>
    /// </summary>
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