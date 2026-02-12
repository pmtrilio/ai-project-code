
using System.CommandLine;
using System.CommandLine.Parsing;
using System.Diagnostics;
using System.Runtime.InteropServices;
using Microsoft.DotNet.Cli.CommandFactory;
using Microsoft.DotNet.Cli.CommandFactory.CommandResolution;
using Microsoft.DotNet.Cli.CommandLine;
using Microsoft.DotNet.Cli.Commands.Run;
using Microsoft.DotNet.Cli.Commands.Workload;
using Microsoft.DotNet.Cli.Extensions;
using Microsoft.DotNet.Cli.ShellShim;
using Microsoft.DotNet.Cli.Telemetry;
using Microsoft.DotNet.Cli.Utils;
using Microsoft.DotNet.Cli.Utils.Extensions;
using Microsoft.DotNet.Configurer;
using Microsoft.DotNet.ProjectTools;
using Microsoft.DotNet.Utilities;
using Microsoft.Extensions.EnvironmentAbstractions;
using NuGet.Frameworks;
using CommandResult = System.CommandLine.Parsing.CommandResult;

namespace Microsoft.DotNet.Cli;

public class Program
{
    private static readonly string ToolPathSentinelFileName = $"{Product.Version}.toolpath.sentinel";

    public static ITelemetry TelemetryClient;
    public static int Main(string[] args)
    {
        // Register a handler for SIGTERM to allow graceful shutdown of the application on Unix.
        // See https://github.com/dotnet/docs/issues/46226.
        using var termSignalRegistration = PosixSignalRegistration.Create(PosixSignal.SIGTERM, _ => Environment.Exit(0));

        using AutomaticEncodingRestorer _ = new();

        if (Env.GetEnvironmentVariable("DOTNET_CLI_CONSOLE_USE_DEFAULT_ENCODING") != "1")
        {
            // Setting output encoding is not available on those platforms
            if (UILanguageOverride.OperatingSystemSupportsUtf8())
            {
                Console.OutputEncoding = Encoding.UTF8;
            }
        }

        DebugHelper.HandleDebugSwitch(ref args);

        // Capture the current timestamp to calculate the host overhead.
        DateTime mainTimeStamp = DateTime.Now;
        TimeSpan startupTime = mainTimeStamp - Process.GetCurrentProcess().StartTime;

        bool perfLogEnabled = Env.GetEnvironmentVariableAsBool("DOTNET_CLI_PERF_LOG", false);

        if (string.IsNullOrEmpty(Env.GetEnvironmentVariable("MSBUILDFAILONDRIVEENUMERATINGWILDCARD")))
        {
            Environment.SetEnvironmentVariable("MSBUILDFAILONDRIVEENUMERATINGWILDCARD", "1");
        }

        // Avoid create temp directory with root permission and later prevent access in non sudo
        if (SudoEnvironmentDirectoryOverride.IsRunningUnderSudo())
        {
            perfLogEnabled = false;
        }

        PerformanceLogStartupInformation startupInfo = null;
        if (perfLogEnabled)
        {
            startupInfo = new PerformanceLogStartupInformation(mainTimeStamp);
            PerformanceLogManager.InitializeAndStartCleanup(FileSystemWrapper.Default);
        }

        PerformanceLogEventListener perLogEventListener = null;
        try
        {
            if (perfLogEnabled)
            {
                perLogEventListener = PerformanceLogEventListener.Create(FileSystemWrapper.Default, PerformanceLogManager.Instance.CurrentLogDirectory);
            }

            PerformanceLogEventSource.Log.LogStartUpInformation(startupInfo);