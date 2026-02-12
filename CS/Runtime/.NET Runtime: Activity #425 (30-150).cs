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
#pragma warning restore CA1825
        private static readonly ActivitySource s_defaultSource = new ActivitySource(string.Empty);
        private static readonly AsyncLocal<Activity?> s_current = new AsyncLocal<Activity?>();

        private const byte ActivityTraceFlagsIsSet = 0b_1_0000000; // Internal flag to indicate if flags have been set
        private const int RequestIdMaxLength = 1024;

        // Used to generate an ID it represents the machine and process we are in.
        private static readonly string s_uniqSuffix = $"-{GetRandomNumber():x}.";

        // A unique number inside the appdomain, randomized between appdomains.
        // Int gives enough randomization and keeps hex-encoded s_currentRootId 8 chars long for most applications
        private static long s_currentRootId = (uint)GetRandomNumber();
        private static ActivityIdFormat s_defaultIdFormat;

        /// <summary>
        /// Event occur when the <see cref="Activity.Current"/> value changes.
        /// </summary>
        public static event EventHandler<ActivityChangedEventArgs>? CurrentChanged;

        /// <summary>
        /// Normally if the ParentID is defined, the format of that is used to determine the
        /// format used by the Activity. However if ForceDefaultFormat is set to true, the
        /// ID format will always be the DefaultIdFormat even if the ParentID is define and is
        /// a different format.
        /// </summary>
        public static bool ForceDefaultIdFormat { get; set; }

        private string? _traceState;
        private State _state;
        private int _currentChildId;  // A unique number for all children of this activity.

        // State associated with ID.
        private string? _id;
        private string? _rootId;
        // State associated with ParentId.
        private string? _parentId;

        // W3C formats
        private string? _parentSpanId;
        private string? _traceId;
        private string? _spanId;

        private byte _w3CIdFlags;
        private byte _parentTraceFlags;

        private TagsLinkedList? _tags;
        private BaggageLinkedList? _baggage;
        private DiagLinkedList<ActivityLink>? _links;
        private DiagLinkedList<ActivityEvent>? _events;
        private Dictionary<string, object>? _customProperties;
        private string? _displayName;
        private ActivityStatusCode _statusCode;
        private string? _statusDescription;
        private Activity? _previousActiveActivity;

        /// <summary>
        /// Gets status code of the current activity object.
        /// </summary>
        public ActivityStatusCode Status => _statusCode;

        /// <summary>
        /// Gets the status description of the current activity object.
        /// </summary>
        public string? StatusDescription => _statusDescription;

        /// <summary>
        /// Gets whether the parent context was created from remote propagation.
        /// </summary>
        public bool HasRemoteParent { get; private set; }

        /// <summary>
        /// Gets or sets the current operation (Activity) for the current thread. This flows
        /// across async calls.
        /// </summary>
        public static Activity? Current
        {
            get { return s_current.Value; }
            set
            {
                if (ValidateSetCurrent(value))
                {
                    SetCurrent(value);
                }
            }