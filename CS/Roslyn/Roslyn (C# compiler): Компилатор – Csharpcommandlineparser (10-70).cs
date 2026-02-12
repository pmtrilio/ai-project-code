using System.IO;
using System.Linq;
using System.Runtime.InteropServices;
using System.Security.Cryptography;
using System.Text;
using System.Threading;
using Microsoft.CodeAnalysis.Emit;
using Microsoft.CodeAnalysis.PooledObjects;
using Microsoft.CodeAnalysis.Text;
using Roslyn.Utilities;

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