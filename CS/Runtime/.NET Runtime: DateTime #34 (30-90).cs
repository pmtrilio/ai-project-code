    // on how the DateTime works except in a context where its specific time
    // zone is needed, such as during conversions and some parsing and formatting
    // cases.
    //
    // There is also 4th state stored that is a special type of Local value that
    // is used to avoid data loss when round-tripping between local and UTC time.
    // See below for more information on this 4th state, although it is
    // effectively hidden from most users, who just see the 3-state DateTimeKind
    // enumeration.
    //
    // For compatibility, DateTime does not serialize the Kind data when used in
    // binary serialization.
    //
    // For a description of various calendar issues, look at
    //
    //
    [StructLayout(LayoutKind.Auto)]
    [Serializable]
    [TypeForwardedFrom("mscorlib, Version=4.0.0.0, Culture=neutral, PublicKeyToken=b77a5c561934e089")]
    public readonly partial struct DateTime
        : IComparable,
          ISpanFormattable,
          IConvertible,
          IComparable<DateTime>,
          IEquatable<DateTime>,
          ISerializable,
          ISpanParsable<DateTime>,
          IUtf8SpanFormattable
    {
        // Number of days in a non-leap year
        private const int DaysPerYear = 365;
        // Number of days in 4 years
        private const int DaysPer4Years = DaysPerYear * 4 + 1;       // 1461
        // Number of days in 100 years
        private const int DaysPer100Years = DaysPer4Years * 25 - 1;  // 36524
        // Number of days in 400 years
        private const int DaysPer400Years = DaysPer100Years * 4 + 1; // 146097

        // Number of days from 1/1/0001 to 12/31/1600
        private const int DaysTo1601 = DaysPer400Years * 4;          // 584388
        // Number of days from 1/1/0001 to 12/30/1899
        private const int DaysTo1899 = DaysPer400Years * 4 + DaysPer100Years * 3 - 367;
        // Number of days from 1/1/0001 to 12/31/1969
        internal const int DaysTo1970 = DaysPer400Years * 4 + DaysPer100Years * 3 + DaysPer4Years * 17 + DaysPerYear; // 719,162
        // Number of days from 1/1/0001 to 12/31/9999
        internal const int DaysTo10000 = DaysPer400Years * 25 - 366;  // 3652059

        internal const long MinTicks = 0;
        internal const long MaxTicks = DaysTo10000 * TimeSpan.TicksPerDay - 1;
        private const long MaxMicroseconds = MaxTicks / TimeSpan.TicksPerMicrosecond;
        private const long MaxMillis = MaxTicks / TimeSpan.TicksPerMillisecond;
        private const long MaxSeconds = MaxTicks / TimeSpan.TicksPerSecond;
        private const long MaxMinutes = MaxTicks / TimeSpan.TicksPerMinute;
        private const long MaxHours = MaxTicks / TimeSpan.TicksPerHour;
        private const long MaxDays = (long)DaysTo10000 - 1;

        internal const long UnixEpochTicks = DaysTo1970 * TimeSpan.TicksPerDay;
        private const long FileTimeOffset = DaysTo1601 * TimeSpan.TicksPerDay;
        private const long DoubleDateOffset = DaysTo1899 * TimeSpan.TicksPerDay;
        // The minimum OA date is 0100/01/01 (Note it's year 100).
        // The maximum OA date is 9999/12/31