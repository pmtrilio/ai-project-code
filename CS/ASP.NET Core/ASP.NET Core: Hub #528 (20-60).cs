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