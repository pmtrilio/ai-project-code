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