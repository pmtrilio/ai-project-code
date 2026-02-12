    using MetadataOrDiagnostic = System.Object;

    public partial class CSharpCompilation
    {
        /// <summary>
        /// ReferenceManager encapsulates functionality to create an underlying SourceAssemblySymbol 
        /// (with underlying ModuleSymbols) for Compilation and AssemblySymbols for referenced
        /// assemblies (with underlying ModuleSymbols) all properly linked together based on
        /// reference resolution between them.
        /// 
        /// ReferenceManager is also responsible for reuse of metadata readers for imported modules
        /// and assemblies as well as existing AssemblySymbols for referenced assemblies. In order
        /// to do that, it maintains global cache for metadata readers and AssemblySymbols
        /// associated with them. The cache uses WeakReferences to refer to the metadata readers and
        /// AssemblySymbols to allow memory and resources being reclaimed once they are no longer
        /// used. The tricky part about reusing existing AssemblySymbols is to find a set of
        /// AssemblySymbols that are created for the referenced assemblies, which (the
        /// AssemblySymbols from the set) are linked in a way, consistent with the reference
        /// resolution between the referenced assemblies.
        /// 
        /// When existing Compilation is used as a metadata reference, there are scenarios when its
        /// underlying SourceAssemblySymbol cannot be used to provide symbols in context of the new
        /// Compilation. Consider classic multi-targeting scenario: compilation C1 references v1 of
        /// Lib.dll and compilation C2 references C1 and v2 of Lib.dll. In this case,
        /// SourceAssemblySymbol for C1 is linked to AssemblySymbol for v1 of Lib.dll. However,
        /// given the set of references for C2, the same reference for C1 should be resolved against
        /// v2 of Lib.dll. In other words, in context of C2, all types from v1 of Lib.dll leaking
        /// through C1 (through method signatures, etc.) must be retargeted to the types from v2 of
        /// Lib.dll. In this case, ReferenceManager creates a special RetargetingAssemblySymbol for
        /// C1, which is responsible for the type retargeting. The RetargetingAssemblySymbols could
        /// also be reused for different Compilations, ReferenceManager maintains a cache of
        /// RetargetingAssemblySymbols (WeakReferences) for each Compilation.
        /// 
        /// The only public entry point of this class is CreateSourceAssembly() method.
        /// </summary>
        internal sealed class ReferenceManager : CommonReferenceManager<CSharpCompilation, AssemblySymbol>
        {
            public ReferenceManager(string simpleAssemblyName, AssemblyIdentityComparer identityComparer, Dictionary<MetadataReference, MetadataOrDiagnostic>? observedMetadata)
                : base(simpleAssemblyName, identityComparer, observedMetadata)
            {
            }

            protected override CommonMessageProvider MessageProvider
            {
                get { return CSharp.MessageProvider.Instance; }
            }

            protected override AssemblyData CreateAssemblyDataForFile(
                PEAssembly assembly,
                WeakList<IAssemblySymbolInternal> cachedSymbols,
                DocumentationProvider documentationProvider,
                string sourceAssemblySimpleName,
                MetadataImportOptions importOptions,
                bool embedInteropTypes)
            {
                return new AssemblyDataForFile(
                    assembly,
                    cachedSymbols,
                    embedInteropTypes,
                    documentationProvider,
                    sourceAssemblySimpleName,
                    importOptions);
            }

            protected override AssemblyData CreateAssemblyDataForCompilation(CompilationReference compilationReference)
            {
                var csReference = compilationReference as CSharpCompilationReference;
                if (csReference == null)
                {
                    throw new NotSupportedException(string.Format(CSharpResources.CantReferenceCompilationOf, compilationReference.GetType(), "C#"));
                }

                var result = new AssemblyDataForCompilation(csReference.Compilation, csReference.Properties.EmbedInteropTypes);
                Debug.Assert(csReference.Compilation._lazyAssemblySymbol is object);
                return result;
            }

            /// <summary>
            /// Checks if the properties of <paramref name="duplicateReference"/> are compatible with properties of <paramref name="primaryReference"/>.
            /// Reports inconsistencies to the given diagnostic bag.
            /// </summary>