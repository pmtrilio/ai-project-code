using System;
using System.Collections.Generic;
using System.Collections.Immutable;
using System.Diagnostics;
using System.Globalization;
using Microsoft.CodeAnalysis.Collections;
using Microsoft.CodeAnalysis.Diagnostics;
using Microsoft.CodeAnalysis.Text;
using Roslyn.Utilities;

namespace Microsoft.CodeAnalysis
{
    /// <summary>
    /// Represents a diagnostic, such as a compiler error or a warning, along with the location where it occurred.
    /// </summary>
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
        /// An optional set of name-value pairs by means of which the analyzer that creates the diagnostic
        /// can convey more detailed information to the fixer. If null, <see cref="Properties"/> will return
        /// <see cref="ImmutableDictionary{TKey, TValue}.Empty"/>.
        /// </param>
        /// <param name="messageArgs">Arguments to the message of the diagnostic.</param>
        /// <returns>The <see cref="Diagnostic"/> instance.</returns>
        public static Diagnostic Create(
            DiagnosticDescriptor descriptor,
            Location? location,
            ImmutableDictionary<string, string?>? properties,
            params object?[]? messageArgs)
        {
            return Create(descriptor, location, null, properties, messageArgs);
        }

        /// <summary>
        /// Creates a <see cref="Diagnostic"/> instance.
        /// </summary>
        /// <param name="descriptor">A <see cref="DiagnosticDescriptor"/> describing the diagnostic.</param>
        /// <param name="location">An optional primary location of the diagnostic. If null, <see cref="Location"/> will return <see cref="Location.None"/>.</param>
        /// <param name="additionalLocations">
        /// An optional set of additional locations related to the diagnostic.
        /// Typically, these are locations of other items referenced in the message.
        /// If null, <see cref="AdditionalLocations"/> will return an empty list.
        /// </param>