// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Diagnostics.CodeAnalysis;
using System.Globalization;
using System.Numerics;
using System.Runtime.CompilerServices;

namespace System
{
    // TimeSpan represents a duration of time.  A TimeSpan can be negative
    // or positive.
    //
    // TimeSpan is internally represented as a number of ticks. A tick is equal
    // to 100 nanoseconds. While this maps well into units of time such as hours
    // and days, any periods longer than that aren't representable in a nice fashion.
    // For instance, a month can be between 28 and 31 days, while a year
    // can contain 365 or 366 days.  A decade can have between 1 and 3 leap years,
    // depending on when you map the TimeSpan into the calendar.  This is why
    // we do not provide Years() or Months().
    //
    [Serializable]
    public readonly struct TimeSpan
        : IComparable,
          IComparable<TimeSpan>,
          IEquatable<TimeSpan>,
          ISpanFormattable,
          ISpanParsable<TimeSpan>,
          IUtf8SpanFormattable
    {
        /// <summary>
        /// Represents the number of nanoseconds per tick. This field is constant.
        /// </summary>
        /// <remarks>
        /// The value of this constant is 100.
        /// </remarks>
        public const long NanosecondsPerTick = 100;                                                 //             100

        /// <summary>
        /// Represents the number of ticks in 1 microsecond. This field is constant.
        /// </summary>
        /// <remarks>
        /// The value of this constant is 10.
        /// </remarks>
        public const long TicksPerMicrosecond = 10;                                                 //              10

        /// <summary>
        /// Represents the number of ticks in 1 millisecond. This field is constant.
        /// </summary>
        /// <remarks>
        /// The value of this constant is 10 thousand; that is, 10,000.
        /// </remarks>
        public const long TicksPerMillisecond = TicksPerMicrosecond * 1000;                         //          10,000

        /// <summary>
        /// Represents the number of ticks in 1 second. This field is constant.
        /// </summary>
        /// <remarks>
        /// The value of this constant is 10 million; that is, 10,000,000.
        /// </remarks>
        public const long TicksPerSecond = TicksPerMillisecond * 1000;                              //      10,000,000

        /// <summary>
        /// Represents the number of ticks in 1 minute. This field is constant.
        /// </summary>
        /// <remarks>
        /// The value of this constant is 600 million; that is, 600,000,000.
        /// </remarks>
        public const long TicksPerMinute = TicksPerSecond * 60;                                     //     600,000,000

        /// <summary>
        /// Represents the number of ticks in 1 hour. This field is constant.
        /// </summary>
        /// <remarks>
        /// The value of this constant is 36 billion; that is, 36,000,000,000.
        /// </remarks>
        public const long TicksPerHour = TicksPerMinute * 60;                                       //  36,000,000,000

        /// <summary>
        /// Represents the number of ticks in 1 day. This field is constant.
        /// </summary>