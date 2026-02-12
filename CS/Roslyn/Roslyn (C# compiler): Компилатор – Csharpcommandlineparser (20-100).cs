
namespace Microsoft.CodeAnalysis.CSharp
{
    public class CSharpCommandLineParser : CommandLineParser
    {
        public static CSharpCommandLineParser Default { get; } = new CSharpCommandLineParser();
        public static CSharpCommandLineParser Script { get; } = new CSharpCommandLineParser(isScriptCommandLineParser: true);

        private static readonly char[] s_quoteOrEquals = new[] { '"', '=' };
        private static readonly char[] s_warningSeparators = new char[] { ',', ';', ' ' };

        internal CSharpCommandLineParser(bool isScriptCommandLineParser = false)
            : base(CSharp.MessageProvider.Instance, isScriptCommandLineParser)
        {
        }

        protected override string RegularFileExtension { get { return ".cs"; } }
        protected override string ScriptFileExtension { get { return ".csx"; } }

        internal sealed override CommandLineArguments CommonParse(IEnumerable<string> args, string baseDirectory, string? sdkDirectory, string? additionalReferenceDirectories)
        {
            return Parse(args, baseDirectory, sdkDirectory, additionalReferenceDirectories);
        }

        /// <summary>
        /// Parses a command line.
        /// </summary>
        /// <param name="args">A collection of strings representing the command line arguments.</param>
        /// <param name="baseDirectory">The base directory used for qualifying file locations.</param>
        /// <param name="sdkDirectory">The directory to search for mscorlib, or null if not available.</param>
        /// <param name="additionalReferenceDirectories">A string representing additional reference paths.</param>
        /// <returns>a commandlinearguments object representing the parsed command line.</returns>
        public new CSharpCommandLineArguments Parse(IEnumerable<string> args, string? baseDirectory, string? sdkDirectory, string? additionalReferenceDirectories = null)
        {
            Debug.Assert(baseDirectory == null || PathUtilities.IsAbsolute(baseDirectory));

            List<Diagnostic> diagnostics = new List<Diagnostic>();
            var flattenedArgs = ArrayBuilder<string>.GetInstance();
            List<string>? scriptArgs = IsScriptCommandLineParser ? new List<string>() : null;
            List<string>? responsePaths = IsScriptCommandLineParser ? new List<string>() : null;
            FlattenArgs(args, diagnostics, flattenedArgs, scriptArgs, baseDirectory, responsePaths);

            string? appConfigPath = null;
            bool displayLogo = true;
            bool displayHelp = false;
            bool displayVersion = false;
            bool displayLangVersions = false;
            bool optimize = false;
            bool checkOverflow = false;
            NullableContextOptions nullableContextOptions = NullableContextOptions.Disable;
            bool allowUnsafe = false;
            bool concurrentBuild = true;
            bool deterministic = false; // TODO(5431): Enable deterministic mode by default
            bool emitPdb = false;
            DebugInformationFormat debugInformationFormat = PathUtilities.IsUnixLikePlatform ? DebugInformationFormat.PortablePdb : DebugInformationFormat.Pdb;
            bool debugPlus = false;
            string? pdbPath = null;
            bool noStdLib = IsScriptCommandLineParser; // don't add mscorlib from sdk dir when running scripts
            bool noSdkPath = false;
            string? outputDirectory = baseDirectory;
            ImmutableArray<KeyValuePair<string, string>> pathMap = ImmutableArray<KeyValuePair<string, string>>.Empty;
            string? outputFileName = null;
            string? outputRefFilePath = null;
            bool refOnly = false;
            string? generatedFilesOutputDirectory = null;
            string? documentationPath = null;
            ErrorLogOptions? errorLogOptions = null;
            bool parseDocumentationComments = false; //Don't just null check documentationFileName because we want to do this even if the file name is invalid.
            bool utf8output = false;
            OutputKind outputKind = OutputKind.ConsoleApplication;
            SubsystemVersion subsystemVersion = SubsystemVersion.None;
            LanguageVersion languageVersion = LanguageVersion.Default;
            string? mainTypeName = null;
            string? win32ManifestFile = null;
            string? win32ResourceFile = null;
            string? win32IconFile = null;
            bool noWin32Manifest = false;
            Platform platform = Platform.AnyCpu;
            ulong baseAddress = 0;
            int fileAlignment = 0;
            bool? delaySignSetting = null;