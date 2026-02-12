using Microsoft.AspNetCore.SignalR.Protocol;

namespace Microsoft.AspNetCore.SignalR;

/// <summary>
/// A lifetime manager abstraction for <see cref="Hub"/> instances.
/// </summary>
public abstract class HubLifetimeManager<THub> where THub : Hub
{
    // Called by the framework and not something we'd cancel, so it doesn't take a cancellation token
    /// <summary>
    /// Called when a connection is started.
    /// </summary>
    /// <param name="connection">The connection.</param>
    /// <returns>A <see cref="Task"/> that represents the asynchronous connect.</returns>
    public abstract Task OnConnectedAsync(HubConnectionContext connection);

    // Called by the framework and not something we'd cancel, so it doesn't take a cancellation token
    /// <summary>
    /// Called when a connection is finished.
    /// </summary>
    /// <param name="connection">The connection.</param>
    /// <returns>A <see cref="Task"/> that represents the asynchronous disconnect.</returns>
    public abstract Task OnDisconnectedAsync(HubConnectionContext connection);

    /// <summary>
    /// Sends an invocation message to all hub connections.
    /// </summary>
    /// <param name="methodName">The invocation method name.</param>
    /// <param name="args">The invocation arguments.</param>
    /// <param name="cancellationToken">The token to monitor for cancellation requests. The default value is <see cref="CancellationToken.None" />.</param>
    /// <returns>A <see cref="Task"/> that represents the asynchronous send.</returns>
    public abstract Task SendAllAsync(string methodName, object?[] args, CancellationToken cancellationToken = default);

    /// <summary>
    /// Sends an invocation message to all hub connections excluding the specified connections.
    /// </summary>
    /// <param name="methodName">The invocation method name.</param>
    /// <param name="args">The invocation arguments.</param>
    /// <param name="excludedConnectionIds">A collection of connection IDs to exclude.</param>
    /// <param name="cancellationToken">The token to monitor for cancellation requests. The default value is <see cref="CancellationToken.None" />.</param>