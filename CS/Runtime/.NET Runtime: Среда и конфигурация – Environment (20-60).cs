        public readonly struct ProcessCpuUsage
        {
            /// <summary>
            /// Gets the amount of time the associated process has spent running code inside the application portion of the process (not the operating system code).
            /// </summary>
            public TimeSpan UserTime { get; internal init; }

            /// <summary>
            /// Gets the amount of time the process has spent running code inside the operating system code.
            /// </summary>
            public TimeSpan PrivilegedTime { get; internal init; }

            /// <summary>
            /// Gets the amount of time the process has spent utilizing the CPU including the process time spent in the application code and the process time spent in the operating system code.
            /// </summary>
            public TimeSpan TotalTime => UserTime + PrivilegedTime;
        }

        /// <summary>
        /// Gets whether the current machine has only a single processor.
        /// </summary>
#if FEATURE_SINGLE_THREADED
        internal const bool IsSingleProcessor = true;
        public static int ProcessorCount => 1;
#else
        internal static bool IsSingleProcessor => ProcessorCount == 1;
        public static int ProcessorCount { get; } = GetProcessorCount();
#endif
        private static volatile sbyte s_privilegedProcess;

        /// <summary>
        /// Gets whether the current process is authorized to perform security-relevant functions.
        /// </summary>
        public static bool IsPrivilegedProcess
        {
            get
            {
                sbyte privilegedProcess = s_privilegedProcess;
                if (privilegedProcess == 0)
                {
                    s_privilegedProcess = privilegedProcess = IsPrivilegedProcessCore() ? (sbyte)1 : (sbyte)-1;