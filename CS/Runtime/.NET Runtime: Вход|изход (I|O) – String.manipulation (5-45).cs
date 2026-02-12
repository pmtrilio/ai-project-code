using System.Collections.Generic;
using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.Globalization;
using System.Numerics;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Runtime.Intrinsics;
using System.Text;

namespace System
{
    public partial class String
    {
        // Avoid paying the init cost of all the SearchValues unless they are actually used.
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