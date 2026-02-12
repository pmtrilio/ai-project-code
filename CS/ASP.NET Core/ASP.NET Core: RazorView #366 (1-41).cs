// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics;
using System.Linq;
using System.Text.Encodings.Web;
using Microsoft.AspNetCore.Mvc.Rendering;
using Microsoft.AspNetCore.Mvc.ViewEngines;
using Microsoft.AspNetCore.Mvc.ViewFeatures.Buffers;
using Microsoft.Extensions.DependencyInjection;

namespace Microsoft.AspNetCore.Mvc.Razor;

/// <summary>
/// Default implementation for <see cref="IView"/> that executes one or more <see cref="IRazorPage"/>
/// as parts of its execution.
/// </summary>
[DebuggerDisplay("{Path,nq}")]
public class RazorView : IView
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