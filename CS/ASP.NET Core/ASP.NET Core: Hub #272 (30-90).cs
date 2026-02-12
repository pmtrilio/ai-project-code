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
    /// </summary>
    /// <returns>A <see cref="Task"/> that represents the asynchronous disconnect.</returns>
    public virtual Task OnDisconnectedAsync(Exception? exception)
    {
        return Task.CompletedTask;
    }

    /// <summary>
    /// Releases all resources currently used by this <see cref="Hub"/> instance.