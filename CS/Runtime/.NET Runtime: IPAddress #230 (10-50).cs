using System.Runtime.InteropServices;
using System.Runtime.Intrinsics;

namespace System.Net
{
    /// <devdoc>
    ///   <para>
    ///     Provides an Internet Protocol (IP) address.
    ///   </para>
    /// </devdoc>
    public class IPAddress : ISpanFormattable, ISpanParsable<IPAddress>, IUtf8SpanFormattable, IUtf8SpanParsable<IPAddress>
    {
        public static readonly IPAddress Any = new ReadOnlyIPAddress([0, 0, 0, 0]);
        public static readonly IPAddress Loopback = new ReadOnlyIPAddress([127, 0, 0, 1]);
        public static readonly IPAddress Broadcast = new ReadOnlyIPAddress([255, 255, 255, 255]);
        public static readonly IPAddress None = Broadcast;

        internal const uint LoopbackMaskHostOrder = 0xFF000000;

        public static readonly IPAddress IPv6Any = new ReadOnlyIPAddress([0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], 0);
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