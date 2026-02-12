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