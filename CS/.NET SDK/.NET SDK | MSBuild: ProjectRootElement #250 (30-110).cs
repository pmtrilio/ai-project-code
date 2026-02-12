    /// <summary>
    /// Event handler for the event fired after this project file is named or renamed.
    /// If the project file has not previously had a name, oldFullPath is null.
    /// </summary>
    internal delegate void RenameHandlerDelegate(string oldFullPath);

    /// <summary>
    /// ProjectRootElement class represents an MSBuild project, an MSBuild targets file or any other file that conforms to MSBuild
    /// project file schema.
    /// This class and its related classes allow a complete MSBuild project or targets file to be read and written.
    /// Comments and whitespace cannot be edited through this model at present.
    ///
    /// Each project root element is associated with exactly one ProjectCollection. This allows the owner of that project collection
    /// to control its lifetime and not be surprised by edits via another project collection.
    /// </summary>
    [DebuggerDisplay("{FullPath} #Children={Count} DefaultTargets={DefaultTargets} ToolsVersion={ToolsVersion} InitialTargets={InitialTargets} ExplicitlyLoaded={IsExplicitlyLoaded}")]
    public partial class ProjectRootElement : ProjectElementContainer
    {
        // Constants for default (empty) project file.
        private const string EmptyProjectFileContent = "{0}<Project{1}{2}>\r\n</Project>";
        private const string EmptyProjectFileXmlDeclaration = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
        private const string EmptyProjectFileToolsVersion = " ToolsVersion=\"" + MSBuildConstants.CurrentToolsVersion + "\"";
        internal const string EmptyProjectFileXmlNamespace = " xmlns=\"http://schemas.microsoft.com/developer/msbuild/2003\"";

        /// <summary>
        /// The singleton delegate that loads projects into the ProjectRootElement
        /// </summary>
        private static readonly ProjectRootElementCacheBase.OpenProjectRootElement s_openLoaderDelegate = OpenLoader;

        private static readonly ProjectRootElementCacheBase.OpenProjectRootElement s_openLoaderPreserveFormattingDelegate = OpenLoaderPreserveFormatting;

        private const string XmlDeclarationPattern = @"\A\s*\<\?\s*xml.*\?\>\s*\Z";

        /// <summary>
        /// Used to determine if a file is an empty XML file if it ONLY contains an XML declaration like &lt;?xml version="1.0" encoding="utf-8"?&gt;.
        /// </summary>
#if NET
        [GeneratedRegex(XmlDeclarationPattern)]
        private static partial Regex XmlDeclarationRegex { get; }
#else
        private static Regex XmlDeclarationRegex => s_xmlDeclarationRegex ??= new Regex(XmlDeclarationPattern);
        private static Regex s_xmlDeclarationRegex;
#endif

        /// <summary>
        /// The default encoding to use / assume for a new project.
        /// </summary>
        private static readonly Encoding s_defaultEncoding = Encoding.UTF8;

        /// <summary>
        /// A global counter used to ensure each project version is distinct from every other.
        /// </summary>
        /// <remarks>
        /// This number is static so that it is unique across the appdomain. That is so that a host
        /// can know when a ProjectRootElement has been unloaded (perhaps after modification) and
        /// reloaded -- the version won't reset to '0'.
        /// </remarks>
        private static int s_globalVersionCounter;

        private int _version;

        /// <summary>
        /// Version number of this object that was last saved to disk, or last loaded from disk.
        /// Used to figure whether this object is dirty for saving.
        /// Saving to or loading from a provided stream reader does not modify this value, only saving to or loading from disk.
        /// The actual value is meaningless (since the counter is shared with all projects) --
        /// it should only be compared to a stored value.
        /// Immediately after loading from disk, this has the same value as <see cref="Version">version</see>.
        /// </summary>
        private int _versionOnDisk;

        /// <summary>
        /// The encoding of the project that was (if applicable) loaded off disk, and that will be used to save the project.
        /// </summary>
        /// <value>Defaults to UTF8 for new projects.</value>
        private Encoding _encoding;

        /// <summary>
        /// XML namespace specified and used by this project file. If a namespace was not specified in the project file, this
        /// value will be string.Empty.
        /// </summary>