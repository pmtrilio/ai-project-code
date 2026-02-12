
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
    /// </summary>
    public RouteData RouteData => ControllerContext.RouteData;

    /// <summary>
    /// Gets the <see cref="ModelStateDictionary"/> that contains the state of the model and of model-binding validation.
    /// </summary>
    public ModelStateDictionary ModelState => ControllerContext.ModelState;

    /// <summary>
    /// Gets or sets the <see cref="Mvc.ControllerContext"/>.
    /// </summary>
    /// <remarks>
    /// <see cref="Controllers.IControllerActivator"/> activates this property while activating controllers.
    /// If user code directly instantiates a controller, the getter returns an empty
    /// <see cref="Mvc.ControllerContext"/>.
    /// </remarks>
    [ControllerContext]
    public ControllerContext ControllerContext
    {
        get
        {
            if (_controllerContext == null)
            {
                _controllerContext = new ControllerContext();
            }

            return _controllerContext;
        }
        set
        {