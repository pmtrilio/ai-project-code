using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Data.Common;
using System.Globalization;
using System.Linq;
using System.Runtime.CompilerServices;
using System.Threading;

namespace Dapper
{
    public static partial class SqlMapper
    {
        /// <summary>
        /// The grid reader provides interfaces for reading multiple result sets from a Dapper query
        /// </summary>
        public partial class GridReader : IDisposable
        {
            private DbDataReader reader;
            private Identity? _identity;
            private readonly bool addToCache;
            private readonly Action<object?>? onCompleted;
            private readonly object? state;
            private readonly CancellationToken cancel;

            /// <summary>
            /// Creates a grid reader over an existing command and reader
            /// </summary>
            [Browsable(false), EditorBrowsable(EditorBrowsableState.Never)]
            protected GridReader(IDbCommand command, DbDataReader reader, Identity? identity, Action<object?>? onCompleted = null, object? state = null, bool addToCache = false, CancellationToken cancellationToken = default)
            {
                Command = command;
                this.reader = reader;
                _identity = identity;
                this.onCompleted = onCompleted;
                this.state = state;
                this.addToCache = addToCache;
                cancel = cancellationToken;
            }

            internal GridReader(IDbCommand command, DbDataReader reader, Identity identity, IParameterCallbacks? callbacks, bool addToCache,
                CancellationToken cancellationToken = default)
                : this(command, reader, identity, callbacks is null ? null : static state => ((IParameterCallbacks)state!).OnCompleted(),
                      callbacks, addToCache, cancellationToken)
            { }

            private Identity Identity => _identity ??= CreateIdentity();

            private Identity CreateIdentity()
            {
                var cmd = Command;
                if (cmd is not null && cmd.Connection is not null)
                {
                    return new Identity(cmd.CommandText, cmd.CommandType, cmd.Connection, null, null);
                }
                throw new InvalidOperationException("This operation requires an identity or a connected command");
            }

            /// <summary>
            /// Read the next grid of results, returned as a dynamic object.