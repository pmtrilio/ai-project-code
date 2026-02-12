
namespace Microsoft.AspNetCore.SignalR;

/// <summary>
/// A base class for a SignalR hub.
/// </summary>
public abstract class Hub : IDisposable
{
    internal const DynamicallyAccessedMemberTypes DynamicallyAccessedMembers = DynamicallyAccessedMemberTypes.PublicConstructors | DynamicallyAccessedMemberTypes.PublicMethods;

    private bool _disposed;
    private IHubCallerClients _clients = default!;
    private HubCallerContext _context = default!;
    private IGroupManager _groups = default!;

    /// <summary>
    /// Gets or sets an object that can be used to invoke methods on the clients connected to this hub.
    /// </summary>
    public IHubCallerClients Clients
    {
        get
        {
            CheckDisposed();
            return _clients;
        }
        set
        {
            CheckDisposed();
            _clients = value;
        }
    }

    /// <summary>
    /// Gets or sets the hub caller context.
    /// </summary>
    public HubCallerContext Context
    {
        get
        {
            CheckDisposed();
            return _context;