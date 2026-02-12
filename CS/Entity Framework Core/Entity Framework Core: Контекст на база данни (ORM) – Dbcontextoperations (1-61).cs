// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Text;
using Microsoft.CodeAnalysis;
using Microsoft.CodeAnalysis.Editing;
using Microsoft.CodeAnalysis.MSBuild;
using Microsoft.EntityFrameworkCore.Infrastructure.Internal;
using Microsoft.EntityFrameworkCore.Internal;
using Microsoft.EntityFrameworkCore.Metadata.Internal;
using Microsoft.EntityFrameworkCore.Query.Internal;
using Microsoft.EntityFrameworkCore.Scaffolding.Internal;

namespace Microsoft.EntityFrameworkCore.Design.Internal;

/// <summary>
///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
///     the same compatibility standards as public APIs. It may be changed or removed without notice in
///     any release. You should only use it directly in your code with extreme caution and knowing that
///     doing so can result in application failures when updating to a new Entity Framework Core release.
/// </summary>
public class DbContextOperations
{
    private readonly IOperationReporter _reporter;
    private readonly Assembly _assembly;
    private readonly Assembly _startupAssembly;
    private readonly string _project;
    private readonly string _projectDir;
    private readonly string? _rootNamespace;
    private readonly string? _language;
    private readonly bool _nullable;
    private readonly string[] _args;
    private readonly AppServiceProviderFactory _appServicesFactory;
    private readonly DesignTimeServicesBuilder _servicesBuilder;

    /// <summary>
    ///     This is an internal API that supports the Entity Framework Core infrastructure and not subject to
    ///     the same compatibility standards as public APIs. It may be changed or removed without notice in
    ///     any release. You should only use it directly in your code with extreme caution and knowing that
    ///     doing so can result in application failures when updating to a new Entity Framework Core release.
    /// </summary>
    public DbContextOperations(
        IOperationReporter reporter,
        Assembly assembly,
        Assembly startupAssembly,
        string project,
        string projectDir,
        string? rootNamespace,
        string? language,
        bool nullable,
        string[]? args)
        : this(
            reporter,
            assembly,
            startupAssembly,
            project,
            projectDir,
            rootNamespace,
            language,
            nullable,
            args,