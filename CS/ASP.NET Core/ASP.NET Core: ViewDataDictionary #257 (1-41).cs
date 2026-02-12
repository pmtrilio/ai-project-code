// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

#nullable enable

using System.Collections;
using System.Diagnostics;
using System.Globalization;
using Microsoft.AspNetCore.Mvc.ModelBinding;
using Microsoft.AspNetCore.Mvc.ModelBinding.Metadata;
using Microsoft.AspNetCore.Shared;
using Microsoft.Extensions.Internal;

namespace Microsoft.AspNetCore.Mvc.ViewFeatures;

/// <summary>
/// A <see cref="IDictionary{TKey, TValue}"/> for view data.
/// </summary>
[DebuggerDisplay("Count = {Count}")]
[DebuggerTypeProxy(typeof(DictionaryDebugView<string, object?>))]
public class ViewDataDictionary : IDictionary<string, object?>
{
    private readonly IDictionary<string, object?> _data;
    private readonly Type _declaredModelType;
    private readonly IModelMetadataProvider _metadataProvider;

    /// <summary>
    /// Initializes a new instance of the <see cref="ViewDataDictionary"/> class.
    /// </summary>
    /// <param name="metadataProvider">
    /// <see cref="IModelMetadataProvider"/> instance used to create <see cref="ViewFeatures.ModelExplorer"/>
    /// instances.
    /// </param>
    /// <param name="modelState"><see cref="ModelStateDictionary"/> instance for this scope.</param>
    /// <remarks>For use when creating a <see cref="ViewDataDictionary"/> for a new top-level scope.</remarks>
    public ViewDataDictionary(
        IModelMetadataProvider metadataProvider,
        ModelStateDictionary modelState)
        : this(metadataProvider, modelState, declaredModelType: typeof(object))
    {
    }