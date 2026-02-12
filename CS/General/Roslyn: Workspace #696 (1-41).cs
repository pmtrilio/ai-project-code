// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.
// See the LICENSE file in the project root for more information.

using System;
using System.Collections.Generic;
using System.Collections.Immutable;
using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.Linq;
using System.Threading;
using System.Threading.Tasks;
using Microsoft.CodeAnalysis;
using Microsoft.CodeAnalysis.Collections;
using Microsoft.CodeAnalysis.Diagnostics;
using Microsoft.CodeAnalysis.ErrorReporting;
using Microsoft.CodeAnalysis.Host;
using Microsoft.CodeAnalysis.Internal.Log;
using Microsoft.CodeAnalysis.Options;
using Microsoft.CodeAnalysis.PooledObjects;
using Microsoft.CodeAnalysis.Shared.Extensions;
using Microsoft.CodeAnalysis.Shared.TestHooks;
using Microsoft.CodeAnalysis.Text;
using Microsoft.CodeAnalysis.Threading;
using Roslyn.Utilities;
using static Microsoft.CodeAnalysis.WorkspaceEventMap;

namespace Microsoft.CodeAnalysis;

/// <summary>
/// A workspace provides access to a active set of source code projects and documents and their
/// associated syntax trees, compilations and semantic models. A workspace has a current solution
/// that is an immutable snapshot of the projects and documents. This property may change over time
/// as the workspace is updated either from live interactions in the environment or via call to the
/// workspace's <see cref="TryApplyChanges(Solution)"/> method.
/// </summary>
public abstract partial class Workspace : IDisposable
{
    private readonly ILegacyGlobalOptionService _legacyOptions;

    private readonly IAsynchronousOperationListener _asyncOperationListener;