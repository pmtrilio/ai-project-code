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
            public dynamic ReadFirst() => ReadRow<dynamic>(typeof(DapperRow), Row.First);

            /// <summary>
            /// Read an individual row of the next grid of results, returned as a dynamic object.
            /// </summary>
            /// <remarks>Note: the row can be accessed via "dynamic", or by casting to an IDictionary&lt;string,object&gt;</remarks>
            public dynamic? ReadFirstOrDefault() => ReadRow<dynamic>(typeof(DapperRow), Row.FirstOrDefault);

            /// <summary>
            /// Read an individual row of the next grid of results, returned as a dynamic object.
            /// </summary>
            /// <remarks>Note: the row can be accessed via "dynamic", or by casting to an IDictionary&lt;string,object&gt;</remarks>
            public dynamic ReadSingle() => ReadRow<dynamic>(typeof(DapperRow), Row.Single);

            /// <summary>
            /// Read an individual row of the next grid of results, returned as a dynamic object.
            /// </summary>
            /// <remarks>Note: the row can be accessed via "dynamic", or by casting to an IDictionary&lt;string,object&gt;</remarks>
            public dynamic? ReadSingleOrDefault() => ReadRow<dynamic>(typeof(DapperRow), Row.SingleOrDefault);

            /// <summary>
            /// Read the next grid of results.
            /// </summary>
            /// <typeparam name="T">The type to read.</typeparam>
            /// <param name="buffered">Whether the results should be buffered in memory.</param>
            public IEnumerable<T> Read<T>(bool buffered = true) => ReadImpl<T>(typeof(T), buffered);

            /// <summary>
            /// Read an individual row of the next grid of results.
            /// </summary>
            /// <typeparam name="T">The type to read.</typeparam>
            public T ReadFirst<T>() => ReadRow<T>(typeof(T), Row.First);

            /// <summary>
            /// Read an individual row of the next grid of results.
            /// </summary>
            /// <typeparam name="T">The type to read.</typeparam>
            public T? ReadFirstOrDefault<T>() => ReadRow<T>(typeof(T), Row.FirstOrDefault);

            /// <summary>
            /// Read an individual row of the next grid of results.
            /// </summary>
            /// <typeparam name="T">The type to read.</typeparam>
            public T ReadSingle<T>() => ReadRow<T>(typeof(T), Row.Single);

            /// <summary>
            /// Read an individual row of the next grid of results.
            /// </summary>
            /// <typeparam name="T">The type to read.</typeparam>
            public T? ReadSingleOrDefault<T>() => ReadRow<T>(typeof(T), Row.SingleOrDefault);

            /// <summary>
            /// Read the next grid of results.
            /// </summary>
            /// <param name="type">The type to read.</param>
            /// <param name="buffered">Whether to buffer the results.</param>
            /// <exception cref="ArgumentNullException"><paramref name="type"/> is <c>null</c>.</exception>
            public IEnumerable<object> Read(Type type, bool buffered = true)
            {
                if (type is null) throw new ArgumentNullException(nameof(type));
                return ReadImpl<object>(type, buffered);
            }

            /// <summary>
            /// Read an individual row of the next grid of results.
            /// </summary>
            /// <param name="type">The type to read.</param>
            /// <exception cref="ArgumentNullException"><paramref name="type"/> is <c>null</c>.</exception>
            public object ReadFirst(Type type)
            {
                if (type is null) throw new ArgumentNullException(nameof(type));
                return ReadRow<object>(type, Row.First);
            }

            /// <summary>
            /// Read an individual row of the next grid of results.
            /// </summary>
            /// <param name="type">The type to read.</param>
            /// <exception cref="ArgumentNullException"><paramref name="type"/> is <c>null</c>.</exception>
            public object? ReadFirstOrDefault(Type type)