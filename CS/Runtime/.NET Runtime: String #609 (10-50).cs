using System.Globalization;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Runtime.Versioning;
using System.Text;

namespace System
{
    // The String class represents a static string of characters.  Many of
    // the string methods perform some type of transformation on the current
    // instance and return the result as a new string.  As with arrays, character
    // positions (indices) are zero-based.

    [Serializable]
    [NonVersionable] // This only applies to field layout
    [TypeForwardedFrom("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089")]
    public sealed partial class String
        : IComparable,
          IEnumerable,
          IConvertible,
          IEnumerable<char>,
          IComparable<string?>,
          IEquatable<string?>,
          ICloneable,
          ISpanParsable<string>
    {
        /// <summary>Maximum length allowed for a string.</summary>
        /// <remarks>Keep in sync with AllocateString in gchelpers.cpp.</remarks>
        internal const int MaxLength = 0x3FFFFFDF;

#if !NATIVEAOT
        // The Empty constant holds the empty string value. It is initialized by the EE during startup.
        // It is treated as intrinsic by the JIT as so the static constructor would never run.
        // Leaving it uninitialized would confuse debuggers.
#pragma warning disable CS8618 // compiler sees this non-nullable static string as uninitialized
        [Intrinsic]
        public static readonly string Empty;
#pragma warning restore CS8618
#endif

        //