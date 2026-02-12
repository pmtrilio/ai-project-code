using System.Runtime.InteropServices;
using System.Runtime.Intrinsics;
using System.Runtime.Intrinsics.Arm;
using System.Runtime.Intrinsics.X86;
using System.Runtime.Versioning;
using System.Text;

namespace System
{
    // Represents a Globally Unique Identifier.
    [StructLayout(LayoutKind.Sequential)]
    [Serializable]
    [NonVersionable] // This only applies to field layout
    [TypeForwardedFrom("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089")]
    public readonly partial struct Guid
        : ISpanFormattable,
          IComparable,
          IComparable<Guid>,
          IEquatable<Guid>,
          ISpanParsable<Guid>,
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