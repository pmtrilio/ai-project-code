// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics.CodeAnalysis;

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
        }
        set
        {
            CheckDisposed();
            _context = value;
        }
    }

    /// <summary>
    /// Gets or sets the group manager.
    /// </summary>
    public IGroupManager Groups
    {
        get
        {
            CheckDisposed();
            return _groups;
        }
        set
        {
            CheckDisposed();
            _groups = value;
        }
    }

    /// <summary>
    /// Called when a new connection is established with the hub.
    /// </summary>
    /// <returns>A <see cref="Task"/> that represents the asynchronous connect.</returns>
    public virtual Task OnConnectedAsync()
    {
        return Task.CompletedTask;
    }

    /// <summary>
    /// Called when a connection with the hub is terminated.