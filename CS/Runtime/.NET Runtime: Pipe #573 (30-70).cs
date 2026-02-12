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
