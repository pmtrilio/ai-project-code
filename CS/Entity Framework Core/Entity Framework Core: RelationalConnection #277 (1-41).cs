// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Collections.Concurrent;
using System.ComponentModel;
using System.Data;
using System.Diagnostics.CodeAnalysis;
using System.Transactions;
using IsolationLevel = System.Data.IsolationLevel;

namespace Microsoft.EntityFrameworkCore.Storage;

/// <summary>
///     <para>
///         Represents a connection with a relational database.
///     </para>
///     <para>
///         This type is typically used by database providers (and other extensions). It is generally
///         not used in application code.
///     </para>
/// </summary>
/// <remarks>
///     <para>
///         The service lifetime is <see cref="ServiceLifetime.Scoped" />. This means that each
///         <see cref="DbContext" /> instance will use its own instance of this service.
///         The implementation may depend on other services registered with any lifetime.
///         The implementation does not need to be thread-safe.
///     </para>
///     <para>
///         See <see href="https://aka.ms/efcore-docs-providers">Implementation of database providers and extensions</see>
///         for more information and examples.
///     </para>
/// </remarks>
public abstract class RelationalConnection : IRelationalConnection, ITransactionEnlistmentManager
{
    private string? _connectionString;
    private bool _connectionOwned;
    private int _openedCount;
    private bool _openedInternally;
    private int? _commandTimeout;
    private readonly int? _defaultCommandTimeout;