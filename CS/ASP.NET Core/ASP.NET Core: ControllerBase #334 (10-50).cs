using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Mvc.Infrastructure;
using Microsoft.AspNetCore.Mvc.ModelBinding;
using Microsoft.AspNetCore.Mvc.ModelBinding.Validation;
using Microsoft.AspNetCore.Mvc.Routing;
using Microsoft.AspNetCore.Routing;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Net.Http.Headers;

namespace Microsoft.AspNetCore.Mvc;

/// <summary>
/// A base class for an MVC controller without view support.
/// </summary>
[Controller]
public abstract class ControllerBase
{
    private ControllerContext? _controllerContext;
    private IModelMetadataProvider? _metadataProvider;
    private IModelBinderFactory? _modelBinderFactory;
    private IObjectModelValidator? _objectValidator;
    private IUrlHelper? _url;
    private ProblemDetailsFactory? _problemDetailsFactory;

    /// <summary>
    /// Gets the <see cref="Http.HttpContext"/> for the executing action.
    /// </summary>
    public HttpContext HttpContext => ControllerContext.HttpContext;

    /// <summary>
    /// Gets the <see cref="HttpRequest"/> for the executing action.
    /// </summary>
    public HttpRequest Request => HttpContext?.Request!;

    /// <summary>
    /// Gets the <see cref="HttpResponse"/> for the executing action.
    /// </summary>
    public HttpResponse Response => HttpContext?.Response!;

    /// <summary>
    /// Gets the <see cref="AspNetCore.Routing.RouteData"/> for the executing action.