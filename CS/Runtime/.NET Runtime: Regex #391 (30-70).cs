        protected internal string[]? capslist;                // if captures are sparse or named captures are used, this is the sorted list of names
        protected internal int capsize;                       // the size of the capture array

        private volatile RegexRunner? _runner;                // cached runner

#if DEBUG
        // These members aren't used from Regex(), but we want to keep them in debug builds for now,
        // so this is a convenient place to include them rather than needing a debug-only illink file.
        [DynamicDependency(nameof(SaveDGML))]
        [DynamicDependency(nameof(GenerateUnicodeTables))]
        [DynamicDependency(nameof(SampleMatches))]
        [DynamicDependency(nameof(Explore))]
#endif
        protected Regex()
        {
            internalMatchTimeout = s_defaultMatchTimeout;
        }

        /// <summary>
        /// Creates a regular expression object for the specified regular expression.
        /// </summary>
        public Regex([StringSyntax(StringSyntaxAttribute.Regex)] string pattern) :
            this(pattern, culture: null)
        {
        }

        /// <summary>
        /// Creates a regular expression object for the specified regular expression, with options that modify the pattern.
        /// </summary>
        public Regex([StringSyntax(StringSyntaxAttribute.Regex, nameof(options))] string pattern, RegexOptions options) :
            this(pattern, options, s_defaultMatchTimeout, culture: null)
        {
        }

        public Regex([StringSyntax(StringSyntaxAttribute.Regex, nameof(options))] string pattern, RegexOptions options, TimeSpan matchTimeout) :
            this(pattern, options, matchTimeout, culture: null)
        {
        }

        internal Regex(string pattern, CultureInfo? culture)
        {