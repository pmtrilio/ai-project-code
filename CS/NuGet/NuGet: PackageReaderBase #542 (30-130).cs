        protected IFrameworkNameProvider FrameworkProvider { get; set; }
        protected IFrameworkCompatibilityProvider CompatibilityProvider { get; set; }

        /// <summary>
        /// Instantiates a new <see cref="PackageReaderBase" /> class.
        /// </summary>
        /// <param name="frameworkProvider">A framework mapping provider.</param>
        /// <exception cref="ArgumentNullException">Thrown if <paramref name="frameworkProvider" /> is <see langword="null" />.</exception>
        public PackageReaderBase(IFrameworkNameProvider frameworkProvider)
            : this(frameworkProvider, new CompatibilityProvider(frameworkProvider))
        {
        }

        /// <summary>
        /// Instantiates a new <see cref="PackageReaderBase" /> class.
        /// </summary>
        /// <param name="frameworkProvider">A framework mapping provider.</param>
        /// <param name="compatibilityProvider">A framework compatibility provider.</param>
        /// <exception cref="ArgumentNullException">Thrown if <paramref name="frameworkProvider" /> is <see langword="null" />.</exception>
        /// <exception cref="ArgumentNullException">Thrown if <paramref name="compatibilityProvider" /> is <see langword="null" />.</exception>
        public PackageReaderBase(IFrameworkNameProvider frameworkProvider, IFrameworkCompatibilityProvider compatibilityProvider)
        {
            if (frameworkProvider == null)
            {
                throw new ArgumentNullException(nameof(frameworkProvider));
            }

            if (compatibilityProvider == null)
            {
                throw new ArgumentNullException(nameof(compatibilityProvider));
            }

            FrameworkProvider = frameworkProvider;
            CompatibilityProvider = compatibilityProvider;
        }

        #region IPackageCoreReader implementation

        public abstract Stream GetStream(string path);

        public abstract IEnumerable<string> GetFiles();

        public abstract IEnumerable<string> GetFiles(string folder);

        public abstract IEnumerable<string> CopyFiles(
            string destination,
            IEnumerable<string> packageFiles,
            ExtractPackageFileDelegate extractFile,
            ILogger logger,
            CancellationToken token);

        public virtual PackageIdentity GetIdentity()
        {
            return NuspecReader.GetIdentity();
        }

        public virtual NuGetVersion GetMinClientVersion()
        {
            return NuspecReader.GetMinClientVersion();
        }

        public virtual IReadOnlyList<PackageType> GetPackageTypes()
        {
            return NuspecReader.GetPackageTypes();
        }

        public virtual Stream GetNuspec()
        {
            // This is the default implementation. It is overridden and optimized in
            // PackageArchiveReader and PackageFolderReader.
            return GetStream(GetNuspecFile());
        }

        public virtual string GetNuspecFile()
        {
            var files = GetFiles();

            return GetNuspecFile(files);
        }

        /// <summary>
        /// Nuspec reader
        /// </summary>
        public virtual NuspecReader NuspecReader
        {
            get
            {
                if (_nuspecReader == null)
                {
                    _nuspecReader = new NuspecReader(GetNuspec());
                }

                return _nuspecReader;
            }
        }

        #endregion

        #region IAsyncPackageCoreReader implementation

        public virtual Task<PackageIdentity> GetIdentityAsync(CancellationToken cancellationToken)