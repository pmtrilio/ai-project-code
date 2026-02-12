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

        /*
         * CONSTRUCTORS
         *
         * Defining a new constructor for string-like types (like String) requires changes both
         * to the managed code below and to the native VM code. See the comment at the top of
         * src/vm/ecall.cpp for instructions on how to add new overloads.
         */

        [MethodImpl(MethodImplOptions.InternalCall)]