
using Microsoft.Build.Framework;
using Microsoft.Build.Utilities;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using Newtonsoft.Json.Serialization;
using NuGet.Frameworks;
using NuGet.ProjectModel;

namespace Microsoft.NET.Build.Tasks
{
    /// <summary>
    /// Generates the $(project).runtimeconfig.json and optionally $(project).runtimeconfig.dev.json files
    /// for a project.
    /// </summary>
    public class GenerateRuntimeConfigurationFiles : TaskBase
    {
        public string AssetsFilePath { get; set; }

        [Required]
        public string TargetFramework { get; set; }

        [Required]
        public string TargetFrameworkMoniker { get; set; }

        [Required]
        public string RuntimeConfigPath { get; set; }

        public string RuntimeConfigDevPath { get; set; }

        public string RuntimeIdentifier { get; set; }

        public string PlatformLibraryName { get; set; }

        public ITaskItem[] RuntimeFrameworks { get; set; }

        public string RollForward { get; set; }

        public string UserRuntimeConfig { get; set; }

        public ITaskItem[] HostConfigurationOptions { get; set; }