        internal static class SearchValuesStorage
        {
            /// <summary>
            /// SearchValues would use SpanHelpers.IndexOfAnyValueType for 5 values in this case.
            /// No need to allocate the SearchValues as a regular Span.IndexOfAny will use the same implementation.
            /// </summary>
            public const string NewLineCharsExceptLineFeed = "\r\f\u0085\u2028\u2029";

            /// <summary>
            /// The Unicode Standard, Sec. 5.8, Recommendation R4 and Table 5-2 state that the CR, LF,
            /// CRLF, NEL, LS, FF, and PS sequences are considered newline functions. That section
            /// also specifically excludes VT from the list of newline functions, so we do not include
            /// it in the needle list.
            /// </summary>
            public static readonly SearchValues<char> NewLineChars =
                SearchValues.Create(NewLineCharsExceptLineFeed + "\n");

            /// <summary>A <see cref="SearchValues{Char}"/> for all of the Unicode whitespace characters</summary>
            public static readonly SearchValues<char> WhiteSpaceChars =
                SearchValues.Create("\t\n\v\f\r\u0020\u0085\u00a0\u1680\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u2028\u2029\u202f\u205f\u3000");

#if DEBUG
            static SearchValuesStorage()
            {
                SearchValues<char> sv = WhiteSpaceChars;
                for (int i = 0; i <= char.MaxValue; i++)
                {
                    Debug.Assert(char.IsWhiteSpace((char)i) == sv.Contains((char)i));
                }
            }
#endif
        }

        internal const int StackallocIntBufferSizeLimit = 128;
        internal const int StackallocCharBufferSizeLimit = 256;

        private static void CopyStringContent(string dest, int destPos, string src)
        {
            Debug.Assert(dest != null);
            Debug.Assert(src != null);
            Debug.Assert(src.Length <= dest.Length - destPos);

            Buffer.Memmove(
                destination: ref Unsafe.Add(ref dest._firstChar, destPos),
                source: ref src._firstChar,
                elementCount: (uint)src.Length);
        }

        public static string Concat(object? arg0) =>
            arg0?.ToString() ?? Empty;
