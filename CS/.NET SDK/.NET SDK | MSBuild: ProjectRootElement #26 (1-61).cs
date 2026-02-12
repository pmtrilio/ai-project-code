// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Globalization;
using System.IO;
using System.Linq;
using System.Runtime.CompilerServices;
using System.Text;
using System.Text.RegularExpressions;
using System.Threading;
using System.Xml;

using Microsoft.Build.Collections;
using Microsoft.Build.Evaluation;
using Microsoft.Build.Eventing;
using Microsoft.Build.Framework;
using Microsoft.Build.Internal;
using Microsoft.Build.ObjectModelRemoting;
using Microsoft.Build.Shared;
using Microsoft.Build.Shared.FileSystem;
using InvalidProjectFileException = Microsoft.Build.Exceptions.InvalidProjectFileException;

#nullable disable

namespace Microsoft.Build.Construction
{
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