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
            /// </summary>
            /// <param name="buffered">Whether the results should be buffered in memory.</param>
            /// <remarks>Note: each row can be accessed via "dynamic", or by casting to an IDictionary&lt;string,object&gt;</remarks>
            public IEnumerable<dynamic> Read(bool buffered = true) => ReadImpl<dynamic>(typeof(DapperRow), buffered);

            /// <summary>
            /// Read an individual row of the next grid of results, returned as a dynamic object.
            /// </summary>
            /// <remarks>Note: the row can be accessed via "dynamic", or by casting to an IDictionary&lt;string,object&gt;</remarks>