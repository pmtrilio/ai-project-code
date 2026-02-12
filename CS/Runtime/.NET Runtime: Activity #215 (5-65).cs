using System.Buffers.Binary;
using System.Buffers.Text;
using System.Collections;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Diagnostics.CodeAnalysis;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Text;
using System.Threading;

namespace System.Diagnostics
{
    /// <summary>
    /// Carries the <see cref="Activity.Current"/> changed event data.
    /// </summary>
    public readonly struct ActivityChangedEventArgs
    {
        internal ActivityChangedEventArgs(Activity? previous, Activity? current)
        {
            Previous = previous;
            Current = current;
        }

        /// <summary>
        /// Gets <see cref="Activity"/> object before the event.
        /// </summary>
        public Activity? Previous { get; init; }

        /// <summary>
        /// Gets <see cref="Activity"/> object after the event.
        /// </summary>
        public Activity? Current { get; init; }
    }

    /// <summary>
    /// Activity represents operation with context to be used for logging.
    /// Activity has operation name, Id, start time and duration, tags and baggage.
    ///
    /// Current activity can be accessed with static AsyncLocal variable Activity.Current.
    ///
    /// Activities should be created with constructor, configured as necessary
    /// and then started with Activity.Start method which maintains parent-child
    /// relationships for the activities and sets Activity.Current.
    ///
    /// When activity is finished, it should be stopped with static Activity.Stop method.
    ///
    /// No methods on Activity allow exceptions to escape as a response to bad inputs.
    /// They are thrown and caught (that allows Debuggers and Monitors to see the error)
    /// but the exception is suppressed, and the operation does something reasonable (typically
    /// doing nothing).
    /// </summary>
    [DebuggerDisplay("{DebuggerDisplayString,nq}")]
    [DebuggerTypeProxy(typeof(ActivityDebuggerProxy))]
    public partial class Activity : IDisposable
    {
#pragma warning disable CA1825 // Array.Empty<T>() doesn't exist in all configurations
        private static readonly IEnumerable<KeyValuePair<string, string?>> s_emptyBaggageTags = new KeyValuePair<string, string?>[0];
        private static readonly IEnumerable<KeyValuePair<string, object?>> s_emptyTagObjects = new KeyValuePair<string, object?>[0];
        private static readonly IEnumerable<ActivityLink> s_emptyLinks = new DiagLinkedList<ActivityLink>();
        private static readonly IEnumerable<ActivityEvent> s_emptyEvents = new DiagLinkedList<ActivityEvent>();