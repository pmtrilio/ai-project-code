using Microsoft.CodeAnalysis.Text;
using Roslyn.Utilities;
using static Microsoft.CodeAnalysis.CSharp.Binder;

namespace Microsoft.CodeAnalysis.CSharp
{
    /// <summary>
    /// The compilation object is an immutable representation of a single invocation of the
    /// compiler. Although immutable, a compilation is also on-demand, and will realize and cache
    /// data as necessary. A compilation can produce a new compilation from existing compilation
    /// with the application of small deltas. In many cases, it is more efficient than creating a
    /// new compilation from scratch, as the new compilation can reuse information from the old
    /// compilation.
    /// </summary>
    public sealed partial class CSharpCompilation : Compilation
    {
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        //
        // Changes to the public interface of this class should remain synchronized with the VB
        // version. Do not make any changes to the public interface without making the corresponding
        // change to the VB version.
        //
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        private readonly CSharpCompilationOptions _options;
        private UsingsFromOptionsAndDiagnostics? _lazyUsingsFromOptions;
        private ImmutableArray<NamespaceOrTypeAndUsingDirective> _lazyGlobalImports;
        private Imports? _lazyPreviousSubmissionImports;
        private AliasSymbol? _lazyGlobalNamespaceAlias;  // alias symbol used to resolve "global::".

        private NamedTypeSymbol? _lazyScriptClass = ErrorTypeSymbol.UnknownResultType;

        // The type of host object model if available.
        private TypeSymbol? _lazyHostObjectTypeSymbol;

        /// <summary>
        /// All imports (using directives and extern aliases) in syntax trees in this compilation.
        /// NOTE: We need to de-dup since the Imports objects that populate the list may be GC'd
        /// and re-created.
        /// Values are the sets of dependencies for corresponding directives.
        /// </summary>
        private ConcurrentDictionary<ImportInfo, ImmutableArray<AssemblySymbol>>? _lazyImportInfos;

        // Cache the CLS diagnostics for the whole compilation so they aren't computed repeatedly.
        // NOTE: Presently, we do not cache the per-tree diagnostics.
        private ImmutableArray<Diagnostic> _lazyClsComplianceDiagnostics;
        private ImmutableArray<AssemblySymbol> _lazyClsComplianceDependencies;

        private Conversions? _conversions;
        /// <summary>
        /// A conversions object that ignores nullability.
        /// </summary>
        internal Conversions Conversions
        {
            get
            {
                if (_conversions == null)
                {
                    Interlocked.CompareExchange(ref _conversions, new BuckStopsHereBinder(this, associatedFileIdentifier: null).Conversions, null);
                }

                return _conversions;
            }
        }

        /// <summary>
        /// Manages anonymous types declared in this compilation. Unifies types that are structurally equivalent.
        /// </summary>
        private AnonymousTypeManager? _lazyAnonymousTypeManager;

        private NamespaceSymbol? _lazyGlobalNamespace;

        private BuiltInOperators? _lazyBuiltInOperators;

        /// <summary>
        /// The <see cref="SourceAssemblySymbol"/> for this compilation. Do not access directly, use Assembly property
        /// instead. This field is lazily initialized by ReferenceManager, ReferenceManager.CacheLockObject must be locked
        /// while ReferenceManager "calculates" the value and assigns it, several threads must not perform duplicate
        /// "calculation" simultaneously.
        /// </summary>
        private SourceAssemblySymbol? _lazyAssemblySymbol;

        /// <summary>
        /// Holds onto data related to reference binding.
        /// The manager is shared among multiple compilations that we expect to have the same result of reference binding.
        /// In most cases this can be determined without performing the binding. If the compilation however contains a circular
        /// metadata reference (a metadata reference that refers back to the compilation) we need to avoid sharing of the binding results.
        /// We do so by creating a new reference manager for such compilation.
        /// </summary>
        private ReferenceManager _referenceManager;

        private readonly SyntaxAndDeclarationManager _syntaxAndDeclarations;

        /// <summary>
        /// Contains the main method of this assembly, if there is one.
        /// </summary>
        private EntryPoint? _lazyEntryPoint;

        /// <summary>
        /// Emit nullable attributes for only those members that are visible outside the assembly
        /// (public, protected, and if any [InternalsVisibleTo] attributes, internal members).