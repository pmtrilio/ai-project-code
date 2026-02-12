        /// This is separated out so that different generators can be used based on how this Random instance is constructed.
        /// If it's built from a seed, then we may need to ensure backwards compatibility for folks expecting consistent sequences
        /// based on that seed.  If the instance is actually derived from Random, then we need to ensure the derived type's
        /// overrides are called anywhere they were being called previously.  But if the instance is the base type and is constructed
        /// with the default constructor, we have a lot of flexibility as to how to optimize the performance and quality of the generator.
        /// </remarks>
        private readonly ImplBase _impl;

        /// <summary>Initializes a new instance of the <see cref="Random"/> class using a default seed value.</summary>
        public Random() =>
            // With no seed specified, if this is the base type, we can implement this however we like.
            // If it's a derived type, for compat we respect the previous implementation, so that overrides
            // are called as they were previously.
            _impl = GetType() == typeof(Random) ? new XoshiroImpl() : new CompatDerivedImpl(this);

        /// <summary>Initializes a new instance of the Random class, using the specified seed value.</summary>
        /// <param name="Seed">
        /// A number used to calculate a starting value for the pseudo-random number sequence. If a negative number
        /// is specified, the absolute value of the number is used.
        /// </param>
        public Random(int Seed) =>
            // With a custom seed, if this is the base Random class, we still need to respect the same algorithm that's been
            // used in the past, but we can do so without having to deal with calling the right overrides in a derived type.
            // If this is a derived type, we need to handle always using the same overrides we've done previously.
            _impl = GetType() == typeof(Random) ? new CompatSeedImpl(Seed) : new CompatDerivedImpl(this, Seed);

        /// <summary>Constructor used by <see cref="ThreadSafeRandom"/>.</summary>
        /// <param name="isThreadSafeRandom">Must be true.</param>
        private protected Random(bool isThreadSafeRandom)
        {
            Debug.Assert(isThreadSafeRandom);
            _impl = null!; // base implementation isn't used at all
        }

        /// <summary>Provides a thread-safe <see cref="Random"/> instance that may be used concurrently from any thread.</summary>
        public static Random Shared { get; } = new ThreadSafeRandom();

        /// <summary>Returns a non-negative random integer.</summary>
        /// <returns>A 32-bit signed integer that is greater than or equal to 0 and less than <see cref="int.MaxValue"/>.</returns>
        public virtual int Next()
        {