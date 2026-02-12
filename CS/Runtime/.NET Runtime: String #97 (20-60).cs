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
        // These fields map directly onto the fields in an EE StringObject.  See object.h for the layout.
        //
        [NonSerialized]
        private readonly int _stringLength;

        // For empty strings, _firstChar will be '\0', since strings are both null-terminated and length-prefixed.
        // The field is also read-only, however String uses .ctors that C# doesn't recognise as .ctors,
        // so trying to mark the field as 'readonly' causes the compiler to complain.
        [NonSerialized]
        private char _firstChar;