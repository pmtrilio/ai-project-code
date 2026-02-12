namespace System
{
    /// <summary>
    /// Represents a pseudo-random number generator, which is an algorithm that produces a sequence of numbers
    /// that meet certain statistical requirements for randomness.
    /// </summary>
    public partial class Random
    {
        /// <summary>The underlying generator implementation.</summary>
        /// <remarks>
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
            int result = _impl.Next();
            AssertInRange(result, 0, int.MaxValue);
            return result;
        }

        /// <summary>Returns a non-negative random integer that is less than the specified maximum.</summary>
        /// <param name="maxValue">The exclusive upper bound of the random number to be generated. <paramref name="maxValue"/> must be greater than or equal to 0.</param>
        /// <returns>
        /// A 32-bit signed integer that is greater than or equal to 0, and less than <paramref name="maxValue"/>; that is, the range of return values ordinarily
        /// includes 0 but not <paramref name="maxValue"/>. However, if <paramref name="maxValue"/> equals 0, <paramref name="maxValue"/> is returned.
        /// </returns>
        /// <exception cref="ArgumentOutOfRangeException"><paramref name="maxValue"/> is less than 0.</exception>
        public virtual int Next(int maxValue)
        {
            ArgumentOutOfRangeException.ThrowIfNegative(maxValue);

            int result = _impl.Next(maxValue);
            AssertInRange(result, 0, maxValue);
            return result;
        }

        /// <summary>Returns a random integer that is within a specified range.</summary>
        /// <param name="minValue">The inclusive lower bound of the random number returned.</param>
        /// <param name="maxValue">The exclusive upper bound of the random number returned. <paramref name="maxValue"/> must be greater than or equal to <paramref name="minValue"/>.</param>
        /// <returns>
        /// A 32-bit signed integer greater than or equal to <paramref name="minValue"/> and less than <paramref name="maxValue"/>; that is, the range of return values includes <paramref name="minValue"/>
        /// but not <paramref name="maxValue"/>. If minValue equals <paramref name="maxValue"/>, <paramref name="minValue"/> is returned.
        /// </returns>
        /// <exception cref="ArgumentOutOfRangeException"><paramref name="minValue"/> is greater than <paramref name="maxValue"/>.</exception>
        public virtual int Next(int minValue, int maxValue)