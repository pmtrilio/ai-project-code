using System.Reflection.Emit;
using System.Runtime.CompilerServices;
using System.Runtime.Serialization;
using System.Text.RegularExpressions.Symbolic;
using System.Threading;

namespace System.Text.RegularExpressions
{
    /// <summary>
    /// Represents an immutable regular expression. Also contains static methods that
    /// allow use of regular expressions without instantiating a Regex explicitly.
    /// </summary>
    public partial class Regex : ISerializable
    {
        [StringSyntax(StringSyntaxAttribute.Regex)]
        protected internal string? pattern;                   // The string pattern provided
        protected internal RegexOptions roptions;             // the top-level options from the options string
        protected internal RegexRunnerFactory? factory;       // Factory used to create runner instances for executing the regex
        protected internal Hashtable? caps;                   // if captures are sparse, this is the hashtable capnum->index
        protected internal Hashtable? capnames;               // if named captures are used, this maps names->index
        protected internal string[]? capslist;                // if captures are sparse or named captures are used, this is the sorted list of names
        protected internal int capsize;                       // the size of the capture array

        private volatile RegexRunner? _runner;                // cached runner

#if DEBUG
        // These members aren't used from Regex(), but we want to keep them in debug builds for now,
        // so this is a convenient place to include them rather than needing a debug-only illink file.
        [DynamicDependency(nameof(SaveDGML))]
        [DynamicDependency(nameof(GenerateUnicodeTables))]
        [DynamicDependency(nameof(SampleMatches))]
        [DynamicDependency(nameof(Explore))]
#endif
        protected Regex()
        {
            internalMatchTimeout = s_defaultMatchTimeout;
        }

        /// <summary>
        /// Creates a regular expression object for the specified regular expression.
        /// </summary>
        public Regex([StringSyntax(StringSyntaxAttribute.Regex)] string pattern) :
            this(pattern, culture: null)
        {
        }

        /// <summary>
        /// Creates a regular expression object for the specified regular expression, with options that modify the pattern.
        /// </summary>
        public Regex([StringSyntax(StringSyntaxAttribute.Regex, nameof(options))] string pattern, RegexOptions options) :
            this(pattern, options, s_defaultMatchTimeout, culture: null)
        {
        }

        public Regex([StringSyntax(StringSyntaxAttribute.Regex, nameof(options))] string pattern, RegexOptions options, TimeSpan matchTimeout) :
            this(pattern, options, matchTimeout, culture: null)
        {
        }

        internal Regex(string pattern, CultureInfo? culture)
        {
            // Validate arguments.
            ValidatePattern(pattern);

            // Parse and store the argument information.
            RegexTree tree = Init(pattern, RegexOptions.None, s_defaultMatchTimeout, ref culture);

            // Create the interpreter factory.
            factory = new RegexInterpreterFactory(tree);

            // NOTE: This overload _does not_ delegate to the one that takes options, in order
            // to avoid unnecessarily rooting the support for RegexOptions.NonBacktracking/Compiler
            // if no options are ever used.
        }

        [UnconditionalSuppressMessage("AotAnalysis", "IL3050:RequiresDynamicCode",
            Justification = "Compiled Regex is only used when RuntimeFeature.IsDynamicCodeCompiled is true. Workaround https://github.com/dotnet/linker/issues/2715.")]
        internal Regex(string pattern, RegexOptions options, TimeSpan matchTimeout, CultureInfo? culture)
        {
            // Validate arguments.
            ValidatePattern(pattern);
            ValidateOptions(options);
            ValidateMatchTimeout(matchTimeout);

            // Parse and store the argument information.
            RegexTree tree = Init(pattern, options, matchTimeout, ref culture);

            // Create the appropriate factory.
            if ((options & RegexOptions.NonBacktracking) != 0)
            {
                // If we're in non-backtracking mode, create the appropriate factory.
                factory = new SymbolicRegexRunnerFactory(tree, options, matchTimeout);
            }
            else
            {
                if (RuntimeFeature.IsDynamicCodeCompiled && (options & RegexOptions.Compiled) != 0)
                {
                    // If the compile option is set and compilation is supported, then compile the code.
                    // If the compiler can't compile this regex, it'll return null, and we'll fall back
                    // to the interpreter.
                    factory = Compile(pattern, tree, options, matchTimeout != InfiniteMatchTimeout);
                }

                // If no factory was created, fall back to creating one for the interpreter.
                factory ??= new RegexInterpreterFactory(tree);
            }
        }

        /// <summary>Stores the supplied arguments and capture information, returning the parsed expression.</summary>
        private RegexTree Init(string pattern, RegexOptions options, TimeSpan matchTimeout, [NotNull] ref CultureInfo? culture)
        {
            this.pattern = pattern;
            roptions = options;
            internalMatchTimeout = matchTimeout;
            culture ??= RegexParser.GetTargetCulture(options);

            // Parse the pattern.
            RegexTree tree = RegexParser.Parse(pattern, options, culture);

            // Store the relevant information, constructing the appropriate factory.
            capnames = tree.CaptureNameToNumberMapping;