        [Required]
        public string RuntimeConfigPath { get; set; }

        public string RuntimeConfigDevPath { get; set; }

        public string RuntimeIdentifier { get; set; }

        public string PlatformLibraryName { get; set; }

        public ITaskItem[] RuntimeFrameworks { get; set; }

        public string RollForward { get; set; }

        public string UserRuntimeConfig { get; set; }

        public ITaskItem[] HostConfigurationOptions { get; set; }

        public ITaskItem[] AdditionalProbingPaths { get; set; }

        public bool IsSelfContained { get; set; }

        public bool WriteAdditionalProbingPathsToMainConfig { get; set; }

        public bool WriteIncludedFrameworks { get; set; }

        public bool GenerateRuntimeConfigDevFile { get; set; }

        public bool AlwaysIncludeCoreFramework { get; set; }

        List<ITaskItem> _filesWritten = new();

        private static readonly string[] RollForwardValues = new string[]
        {
            "Disable",
            "LatestPatch",
            "Minor",
            "LatestMinor",
            "Major",
            "LatestMajor"
        };

        [Output]
        public ITaskItem[] FilesWritten
        {
            get { return _filesWritten.ToArray(); }
        }

        protected override void ExecuteCore()
        {
            if (!WriteAdditionalProbingPathsToMainConfig)
            {
                // If we want to generate the runtimeconfig.dev.json file
                // and we have additional probing paths to add to it
                // BUT the runtimeconfigdevpath is empty, log a warning.
                if (GenerateRuntimeConfigDevFile && AdditionalProbingPaths?.Any() == true && string.IsNullOrEmpty(RuntimeConfigDevPath))
                {
                    Log.LogWarning(Strings.SkippingAdditionalProbingPaths);
                }
            }

            if (!string.IsNullOrEmpty(RollForward))
            {
                if (!RollForwardValues.Contains(RollForward, StringComparer.OrdinalIgnoreCase))
                {
                    Log.LogError(Strings.InvalidRollForwardValue, RollForward, string.Join(", ", RollForwardValues));
                    return;
                }
            }

            if (AssetsFilePath == null)
            {
                var isFrameworkDependent = LockFileExtensions.IsFrameworkDependent(
                    RuntimeFrameworks,
                    IsSelfContained,
                    RuntimeIdentifier,
                    string.IsNullOrWhiteSpace(PlatformLibraryName));

                if (isFrameworkDependent != true)
                {
                    throw new ArgumentException(
                        $"{nameof(DependencyContextBuilder)} Does not support non FrameworkDependent without asset file. " +