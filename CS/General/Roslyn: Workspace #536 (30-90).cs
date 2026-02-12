/// <summary>
/// A workspace provides access to a active set of source code projects and documents and their
/// associated syntax trees, compilations and semantic models. A workspace has a current solution
/// that is an immutable snapshot of the projects and documents. This property may change over time
/// as the workspace is updated either from live interactions in the environment or via call to the
/// workspace's <see cref="TryApplyChanges(Solution)"/> method.
/// </summary>
public abstract partial class Workspace : IDisposable
{
    private readonly ILegacyGlobalOptionService _legacyOptions;

    private readonly IAsynchronousOperationListener _asyncOperationListener;

    private readonly AsyncBatchingWorkQueue<(EventArgs, EventHandlerSet)> _eventHandlerWorkQueue;
    private readonly CancellationTokenSource _workQueueTokenSource = new();
    private readonly ITaskSchedulerProvider _taskSchedulerProvider;

    // forces serialization of mutation calls from host (OnXXX methods). Must take this lock before taking stateLock.
    private readonly SemaphoreSlim _serializationLock = new(initialCount: 1);

    // this lock guards all the mutable fields (do not share lock with derived classes)
    private readonly NonReentrantLock _stateLock = new(useThisInstanceForSynchronization: true);

    /// <summary>
    /// Cache for initializing generator drivers across different Solution instances from this Workspace.
    /// </summary>
    internal SolutionCompilationState.GeneratorDriverInitializationCache GeneratorDriverCreationCache { get; } = new();

    /// <summary>
    /// Current solution.  Must be locked with <see cref="_serializationLock"/> when writing to it.
    /// </summary>
    private Solution _latestSolution;

    /// <summary>
    /// Determines whether changes made to unchangeable documents will be silently ignored or cause exceptions to be thrown
    /// when they are applied to workspace via <see cref="TryApplyChanges(Solution, IProgress{CodeAnalysisProgress})"/>. 
    /// A document is unchangeable if <see cref="IDocumentOperationService.CanApplyChange"/> is false.
    /// </summary>
    internal virtual bool IgnoreUnchangeableDocumentsWhenApplyingChanges { get; } = false;

    /// <summary>
    /// Constructs a new workspace instance.
    /// </summary>
    /// <param name="host">The <see cref="HostServices"/> this workspace uses</param>
    /// <param name="workspaceKind">A string that can be used to identify the kind of workspace. Usually this matches the name of the class.</param>
    protected Workspace(HostServices host, string? workspaceKind)
    {
        Kind = workspaceKind;

        Services = host.CreateWorkspaceServices(this);

        _legacyOptions = Services.GetRequiredService<ILegacyWorkspaceOptionService>().LegacyGlobalOptions;
        _legacyOptions.RegisterWorkspace(this);

        // queue used for sending events
        _taskSchedulerProvider = Services.GetRequiredService<ITaskSchedulerProvider>();

        var listenerProvider = Services.GetRequiredService<IWorkspaceAsynchronousOperationListenerProvider>();
        _asyncOperationListener = listenerProvider.GetListener();
        _eventHandlerWorkQueue = new(
            TimeSpan.Zero,