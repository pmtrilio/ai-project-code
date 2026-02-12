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
    private volatile bool _resetting;
    private readonly ConcurrentStack<Transaction> _ambientTransactions = new();
    private DbConnection? _connection;
    private readonly IRelationalCommandBuilder _relationalCommandBuilder;
    private readonly IExceptionDetector _exceptionDetector;
    private IRelationalCommand? _cachedRelationalCommand;

    /// <summary>
    ///     Initializes a new instance of the <see cref="RelationalConnection" /> class.
    /// </summary>
    /// <param name="dependencies">Parameter object containing dependencies for this service.</param>
    protected RelationalConnection(RelationalConnectionDependencies dependencies)
    {
        Context = dependencies.CurrentContext.Context;
        _relationalCommandBuilder = dependencies.RelationalCommandBuilderFactory.Create();

        Dependencies = dependencies;

        var relationalOptions = RelationalOptionsExtension.Extract(dependencies.ContextOptions);

        _defaultCommandTimeout = _commandTimeout = relationalOptions.CommandTimeout;

        _connectionString = string.IsNullOrWhiteSpace(relationalOptions.ConnectionString)
            ? null
            : dependencies.ConnectionStringResolver.ResolveConnectionString(relationalOptions.ConnectionString);

        if (relationalOptions.Connection != null)
        {
            _connection = relationalOptions.Connection;
            _connectionOwned = relationalOptions.IsConnectionOwned;

            Check.DebugAssert(_connectionString == null, "ConnectionString is not null");
        }
        else
        {
            _connectionOwned = true;
        }

        _exceptionDetector = dependencies.ExceptionDetector;
    }

    /// <summary>
    ///     The unique identifier for this connection.
    /// </summary>