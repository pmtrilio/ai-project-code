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
