          IUtf8SpanFormattable,
          IUtf8SpanParsable<Guid>
    {
        private const byte Variant10xxMask = 0xC0;
        private const byte Variant10xxValue = 0x80;

        private const ushort VersionMask = 0xF000;
        private const ushort Version4Value = 0x4000;
        private const ushort Version7Value = 0x7000;

        public static readonly Guid Empty;

        /// <summary>Gets a <see cref="Guid" /> where all bits are set.</summary>
        /// <remarks>This returns the value: FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF</remarks>
        public static Guid AllBitsSet => new Guid(uint.MaxValue, ushort.MaxValue, ushort.MaxValue, byte.MaxValue, byte.MaxValue, byte.MaxValue, byte.MaxValue, byte.MaxValue, byte.MaxValue, byte.MaxValue, byte.MaxValue);

        private readonly int _a;   // Do not rename (binary serialization)
        private readonly short _b; // Do not rename (binary serialization)
        private readonly short _c; // Do not rename (binary serialization)
        private readonly byte _d;  // Do not rename (binary serialization)
        private readonly byte _e;  // Do not rename (binary serialization)
        private readonly byte _f;  // Do not rename (binary serialization)
        private readonly byte _g;  // Do not rename (binary serialization)
        private readonly byte _h;  // Do not rename (binary serialization)
        private readonly byte _i;  // Do not rename (binary serialization)
        private readonly byte _j;  // Do not rename (binary serialization)
        private readonly byte _k;  // Do not rename (binary serialization)

        // Creates a new guid from an array of bytes.
        public Guid(byte[] b) :
            this(new ReadOnlySpan<byte>(b ?? throw new ArgumentNullException(nameof(b))))
        {
        }

        // Creates a new guid from a read-only span.
        public Guid(ReadOnlySpan<byte> b)
        {
            if (b.Length != 16)
            {
                ThrowGuidArrayCtorArgumentException();
            }

            this = MemoryMarshal.Read<Guid>(b);

            if (!BitConverter.IsLittleEndian)
            {
                _a = BinaryPrimitives.ReverseEndianness(_a);
                _b = BinaryPrimitives.ReverseEndianness(_b);
                _c = BinaryPrimitives.ReverseEndianness(_c);
            }
        }

        public Guid(ReadOnlySpan<byte> b, bool bigEndian)
        {
            if (b.Length != 16)
            {
                ThrowGuidArrayCtorArgumentException();
            }

            this = MemoryMarshal.Read<Guid>(b);

            if (BitConverter.IsLittleEndian == bigEndian)
            {
                _a = BinaryPrimitives.ReverseEndianness(_a);
                _b = BinaryPrimitives.ReverseEndianness(_b);
                _c = BinaryPrimitives.ReverseEndianness(_c);
            }
        }

        [DoesNotReturn]
        [StackTraceHidden]
        private static void ThrowGuidArrayCtorArgumentException()
        {
            throw new ArgumentException(SR.Format(SR.Arg_GuidArrayCtor, "16"), "b");
        }

        [CLSCompliant(false)]
        public Guid(uint a, ushort b, ushort c, byte d, byte e, byte f, byte g, byte h, byte i, byte j, byte k)
        {
            _a = (int)a;
            _b = (short)b;
            _c = (short)c;
            _d = d;
            _e = e;
            _f = f;
            _g = g;
            _h = h;
            _i = i;
            _j = j;
            _k = k;
        }

        // Creates a new GUID initialized to the value represented by the arguments.
        public Guid(int a, short b, short c, byte[] d)
        {
            ArgumentNullException.ThrowIfNull(d);

            if (d.Length != 8)
            {
                throw new ArgumentException(SR.Format(SR.Arg_GuidArrayCtor, "8"), nameof(d));
            }