        /// </summary>
        public new CSharpCompilation Compilation { get; }

        internal override Compilation CompilationCore
        {
            get { return this.Compilation; }
        }

        /// <summary>
        /// Create a metadata reference to a compilation.
        /// </summary>
        /// <param name="compilation">The compilation to reference.</param>
        /// <param name="aliases">Extern aliases for this reference.</param>
        /// <param name="embedInteropTypes">Should interop types be embedded in the created assembly?</param>
        public CSharpCompilationReference(
            CSharpCompilation compilation,
            ImmutableArray<string> aliases = default(ImmutableArray<string>),
            bool embedInteropTypes = false)
            : base(GetProperties(compilation, aliases, embedInteropTypes))
        {
            this.Compilation = compilation;
        }

        private CSharpCompilationReference(CSharpCompilation compilation, MetadataReferenceProperties properties)
            : base(properties)
        {
            this.Compilation = compilation;
        }

        internal override CompilationReference WithPropertiesImpl(MetadataReferenceProperties properties)
        {
            return new CSharpCompilationReference(Compilation, properties);
        }

        private string GetDebuggerDisplay()
        {
            return CSharpResources.CompilationC + this.Compilation.AssemblyName;
        }
    }
}
