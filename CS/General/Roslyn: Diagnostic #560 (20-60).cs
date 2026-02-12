    [DebuggerDisplay("{GetDebuggerDisplay(), nq}")]
    public abstract partial class Diagnostic : IEquatable<Diagnostic?>, IFormattable
    {
        internal const string CompilerDiagnosticCategory = "Compiler";

        /// <summary>
        /// The default warning level, which is also used for non-error diagnostics.
        /// </summary>
        internal const int DefaultWarningLevel = 4;

        /// <summary>
        /// The warning level used for hidden and info diagnostics. Because these diagnostics interact with other editor features, we want them to always be produced unless /warn:0 is set.
        /// </summary>
        internal const int InfoAndHiddenWarningLevel = 1;

        /// <summary>
        /// The maximum warning level represented by a large value of 9999.
        /// </summary>
        internal const int MaxWarningLevel = 9999;

        /// <summary>
        /// Creates a <see cref="Diagnostic"/> instance.
        /// </summary>
        /// <param name="descriptor">A <see cref="DiagnosticDescriptor"/> describing the diagnostic</param>
        /// <param name="location">An optional primary location of the diagnostic. If null, <see cref="Location"/> will return <see cref="Location.None"/>.</param>
        /// <param name="messageArgs">Arguments to the message of the diagnostic</param>
        /// <returns>The <see cref="Diagnostic"/> instance.</returns>
        public static Diagnostic Create(
            DiagnosticDescriptor descriptor,
            Location? location,
            params object?[]? messageArgs)
        {
            return Create(descriptor, location, null, null, messageArgs);
        }

        /// <summary>
        /// Creates a <see cref="Diagnostic"/> instance.
        /// </summary>
        /// <param name="descriptor">A <see cref="DiagnosticDescriptor"/> describing the diagnostic.</param>
        /// <param name="location">An optional primary location of the diagnostic. If null, <see cref="Location"/> will return <see cref="Location.None"/>.</param>
        /// <param name="properties">