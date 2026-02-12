        private static readonly TimeSpan s_maxTimeout = TimeSpan.FromMilliseconds(int.MaxValue);
        private static readonly TimeSpan s_infiniteTimeout = Threading.Timeout.InfiniteTimeSpan;
        private const HttpCompletionOption DefaultCompletionOption = HttpCompletionOption.ResponseContentRead;

        private volatile bool _operationStarted;
        private volatile bool _disposed;

        private CancellationTokenSource _pendingRequestsCts;
        private HttpRequestHeaders? _defaultRequestHeaders;
        private Version _defaultRequestVersion = HttpRequestMessage.DefaultRequestVersion;
        private HttpVersionPolicy _defaultVersionPolicy = HttpRequestMessage.DefaultVersionPolicy;

        private Uri? _baseAddress;
        private TimeSpan _timeout;
        private int _maxResponseContentBufferSize;

        #endregion Fields

        #region Properties
        public static IWebProxy DefaultProxy
        {
            get => LazyInitializer.EnsureInitialized(ref s_defaultProxy, () => SystemProxyInfo.Proxy);
            set
            {
                ArgumentNullException.ThrowIfNull(value);
                s_defaultProxy = value;
            }
        }

        public HttpRequestHeaders DefaultRequestHeaders =>
            _defaultRequestHeaders ??= new HttpRequestHeaders();

        public Version DefaultRequestVersion
        {
            get => _defaultRequestVersion;
            set
            {
                CheckDisposedOrStarted();
                ArgumentNullException.ThrowIfNull(value);
                _defaultRequestVersion = value;
            }
        }

        /// <summary>
        /// Gets or sets the default value of <see cref="HttpRequestMessage.VersionPolicy" /> for implicitly created requests in convenience methods,
        /// e.g.: <see cref="GetAsync(string?)" />, <see cref="PostAsync(string?, HttpContent)" />.
        /// </summary>
        /// <remarks>
        /// Note that this property has no effect on any of the <see cref="Send(HttpRequestMessage)" /> and <see cref="SendAsync(HttpRequestMessage)" /> overloads
        /// since they accept fully initialized <see cref="HttpRequestMessage" />.
        /// </remarks>
        public HttpVersionPolicy DefaultVersionPolicy
        {
            get => _defaultVersionPolicy;
            set
            {
                CheckDisposedOrStarted();
                _defaultVersionPolicy = value;
            }
        }

        public Uri? BaseAddress
        {
            get => _baseAddress;
            set
            {
                // It's OK to not have a base address specified, but if one is, it needs to be absolute.
                if (value is not null && !value.IsAbsoluteUri)
                {
                    throw new ArgumentException(SR.net_http_client_absolute_baseaddress_required, nameof(value));
                }

                CheckDisposedOrStarted();

                if (NetEventSource.Log.IsEnabled()) NetEventSource.UriBaseAddress(this, value);

                _baseAddress = value;
            }
        }

        public TimeSpan Timeout