using System.Collections.Generic;
using System.Diagnostics;
using System.Runtime.CompilerServices;
using System.Threading;
using System.Threading.Tasks;
using System.Threading.Tasks.Sources;

namespace System.IO.Pipelines
{
    /// <summary>The default <see cref="System.IO.Pipelines.PipeWriter" /> and <see cref="System.IO.Pipelines.PipeReader" /> implementation.</summary>
    public sealed partial class Pipe
    {
        private static readonly Action<object?> s_signalReaderAwaitable = state => ((Pipe)state!).ReaderCancellationRequested();
        private static readonly Action<object?> s_signalWriterAwaitable = state => ((Pipe)state!).WriterCancellationRequested();
        private static readonly Action<object?> s_invokeCompletionCallbacks = state => ((PipeCompletionCallbacks)state!).Execute();

        // These callbacks all point to the same methods but are different delegate types
        private static readonly ContextCallback s_executionContextRawCallback = ExecuteWithoutExecutionContext!;
        private static readonly SendOrPostCallback s_syncContextExecutionContextCallback = ExecuteWithExecutionContext!;
        private static readonly SendOrPostCallback s_syncContextExecuteWithoutExecutionContextCallback = ExecuteWithoutExecutionContext!;
        private static readonly Action<object?> s_scheduleWithExecutionContextCallback = ExecuteWithExecutionContext!;

        // Mutable struct! Don't make this readonly
        private BufferSegmentStack _bufferSegmentPool;

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
