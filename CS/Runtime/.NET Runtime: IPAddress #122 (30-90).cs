        public static readonly IPAddress IPv6Loopback = new ReadOnlyIPAddress([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1], 0);
        public static readonly IPAddress IPv6None = IPv6Any;

        private static readonly IPAddress s_loopbackMappedToIPv6 = new ReadOnlyIPAddress([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 255, 255, 127, 0, 0, 1], 0);

        /// <summary>
        /// For IPv4 addresses, this field stores the Address.
        /// For IPv6 addresses, this field stores the ScopeId.
        /// Instead of accessing this field directly, use the <see cref="PrivateAddress"/> or <see cref="PrivateScopeId"/> properties.
        /// </summary>
        private uint _addressOrScopeId;

        /// <summary>
        /// This field is only used for IPv6 addresses. A null value indicates that this instance is an IPv4 address.
        /// </summary>
        private readonly ushort[]? _numbers;

        /// <summary>
        /// A lazily initialized cache of the result of calling <see cref="ToString"/>.
        /// </summary>
        private string? _toString;

        /// <summary>
        /// A lazily initialized cache of the <see cref="GetHashCode"/> value.
        /// </summary>
        private int _hashCode;

        internal const int NumberOfLabels = IPAddressParserStatics.IPv6AddressBytes / 2;

        [MemberNotNullWhen(false, nameof(_numbers))]
        private bool IsIPv4
        {
            get { return _numbers == null; }
        }

        [MemberNotNullWhen(true, nameof(_numbers))]
        private bool IsIPv6
        {
            get { return _numbers != null; }
        }

        internal uint PrivateAddress
        {
            get
            {
                Debug.Assert(IsIPv4);
                return _addressOrScopeId;
            }
            private set
            {
                Debug.Assert(IsIPv4);
                _toString = null;
                _hashCode = 0;
                _addressOrScopeId = value;
            }
        }

        internal uint PrivateIPv4Address
        {
            get
            {