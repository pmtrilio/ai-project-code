using System;
using System.Buffers.Binary;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Collections.Immutable;
using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.IO;
using System.Linq;
using System.Reflection;
using System.Reflection.Metadata;
using System.Threading;
using Microsoft.Cci;
using Microsoft.CodeAnalysis;
using Microsoft.CodeAnalysis.CodeGen;
using Microsoft.CodeAnalysis.Collections;
using Microsoft.CodeAnalysis.CSharp.Emit;
using Microsoft.CodeAnalysis.CSharp.Symbols;
using Microsoft.CodeAnalysis.CSharp.Syntax;
using Microsoft.CodeAnalysis.Debugging;
using Microsoft.CodeAnalysis.Diagnostics;
using Microsoft.CodeAnalysis.Emit;
using Microsoft.CodeAnalysis.Operations;
using Microsoft.CodeAnalysis.PooledObjects;
using Microsoft.CodeAnalysis.Symbols;
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