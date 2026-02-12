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