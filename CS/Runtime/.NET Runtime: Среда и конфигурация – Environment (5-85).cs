using System.Diagnostics;
using System.Runtime.CompilerServices;
using System.Threading;

namespace System
{
    public static partial class Environment
    {
        /// <summary>
        /// Represents the CPU usage statistics of a process.
        /// </summary>
        /// <remarks>
        /// The CPU usage statistics include information about the time spent by the process in the application code (user mode) and the operating system code (kernel mode),
        /// as well as the total time spent by the process in both user mode and kernel mode.
        /// </remarks>
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
                }
                return privilegedProcess > 0;
            }
        }

        // Unconditionally return false since .NET Core does not support object finalization during shutdown.
        public static bool HasShutdownStarted => false;

        public static string? GetEnvironmentVariable(string variable)
        {
            ArgumentNullException.ThrowIfNull(variable);

            return GetEnvironmentVariableCore(variable);
        }

        public static string? GetEnvironmentVariable(string variable, EnvironmentVariableTarget target)
        {
            if (target == EnvironmentVariableTarget.Process)
                return GetEnvironmentVariable(variable);

            ArgumentNullException.ThrowIfNull(variable);

            bool fromMachine = ValidateAndConvertRegistryTarget(target);
            return GetEnvironmentVariableFromRegistry(variable, fromMachine);
        }