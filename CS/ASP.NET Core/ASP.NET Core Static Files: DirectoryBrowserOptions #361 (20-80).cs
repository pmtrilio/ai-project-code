    }

    /// <summary>
    /// Enabled directory browsing all request paths
    /// </summary>
    /// <param name="sharedOptions"></param>
    public DirectoryBrowserOptions(SharedOptions sharedOptions)
        : base(sharedOptions)
    {
    }

    /// <summary>
    /// The component that generates the view.
    /// </summary>
    public IDirectoryFormatter? Formatter { get; set; }
}
