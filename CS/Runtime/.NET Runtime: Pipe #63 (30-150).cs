        private readonly DefaultPipeReader _reader;
        private readonly DefaultPipeWriter _writer;

        // The options instance
        private readonly PipeOptions _options;
        private readonly object _sync = new object();

        // Computed state from the options instance
        private bool UseSynchronizationContext => _options.UseSynchronizationContext;
        private int MinimumSegmentSize => _options.MinimumSegmentSize;
        private long PauseWriterThreshold => _options.PauseWriterThreshold;
        private long ResumeWriterThreshold => _options.ResumeWriterThreshold;

        private PipeScheduler ReaderScheduler => _options.ReaderScheduler;
        private PipeScheduler WriterScheduler => _options.WriterScheduler;

        // This sync objects protects the shared state between the writer and reader (most of this class)
        private object SyncObj => _sync;

        // The number of bytes flushed but not consumed by the reader
        private long _unconsumedBytes;

        // The number of bytes written but not flushed
        private long _unflushedBytes;

        private PipeAwaitable _readerAwaitable;
        private PipeAwaitable _writerAwaitable;

        private PipeCompletion _writerCompletion;
        private PipeCompletion _readerCompletion;

        // Stores the last examined position, used to calculate how many bytes were to release
        // for back pressure management
        private long _lastExaminedIndex = -1;

        // The read head which is the start of the PipeReader's consumed bytes
        private BufferSegment? _readHead;
        private int _readHeadIndex;

        private bool _disposed;

        // The extent of the bytes available to the PipeReader to consume
        private BufferSegment? _readTail;
        private int _readTailIndex;
        private int _minimumReadBytes;

        // The write head which is the extent of the PipeWriter's written bytes
        private BufferSegment? _writingHead;
        private Memory<byte> _writingHeadMemory;
        private int _writingHeadBytesBuffered;

        // Determines what current operation is in flight (reading/writing)
        private PipeOperationState _operationState;

        internal long Length => _unconsumedBytes;

        /// <summary>Initializes a new instance of the <see cref="System.IO.Pipelines.Pipe" /> class using <see cref="System.IO.Pipelines.PipeOptions.Default" /> as options.</summary>
        public Pipe() : this(PipeOptions.Default)
        {
        }

        /// <summary>Initializes a new instance of the <see cref="System.IO.Pipelines.Pipe" /> class with the specified options.</summary>
        /// <param name="options">The set of options for this pipe.</param>
        public Pipe(PipeOptions options)
        {
            if (options == null)
            {
                ThrowHelper.ThrowArgumentNullException(ExceptionArgument.options);
            }

            _bufferSegmentPool = new BufferSegmentStack(options.InitialSegmentPoolSize);

            _operationState = default;
            _readerCompletion = default;
            _writerCompletion = default;

            _options = options;
            _readerAwaitable = new PipeAwaitable(completed: false, UseSynchronizationContext);
            _writerAwaitable = new PipeAwaitable(completed: true, UseSynchronizationContext);
            _reader = new DefaultPipeReader(this);
            _writer = new DefaultPipeWriter(this);
        }

        private void ResetState()
        {
            _readerCompletion.Reset();
            _writerCompletion.Reset();
            _readerAwaitable = new PipeAwaitable(completed: false, UseSynchronizationContext);
            _writerAwaitable = new PipeAwaitable(completed: true, UseSynchronizationContext);
            _readTailIndex = 0;
            _readHeadIndex = 0;
            _lastExaminedIndex = -1;
            _unflushedBytes = 0;
            _unconsumedBytes = 0;
        }

        internal Memory<byte> GetMemory(int sizeHint)
        {
            if (_writerCompletion.IsCompleted)
            {
                ThrowHelper.ThrowInvalidOperationException_NoWritingAllowed();
            }

            if (sizeHint < 0)
            {
                ThrowHelper.ThrowArgumentOutOfRangeException(ExceptionArgument.sizeHint);
            }

            AllocateWriteHeadIfNeeded(sizeHint);

            return _writingHeadMemory;
        }

        internal Span<byte> GetSpan(int sizeHint)
        {
            if (_writerCompletion.IsCompleted)
            {
                ThrowHelper.ThrowInvalidOperationException_NoWritingAllowed();
            }

            if (sizeHint < 0)