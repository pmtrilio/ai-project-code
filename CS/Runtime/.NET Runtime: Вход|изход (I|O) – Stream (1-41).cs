// Licensed to the .NET Foundation under one or more agreements.
// The .NET Foundation licenses this file to you under the MIT license.

using System.Buffers;
using System.Diagnostics;
using System.Diagnostics.CodeAnalysis;
using System.Runtime.CompilerServices;
using System.Runtime.InteropServices;
using System.Threading;
using System.Threading.Tasks;

namespace System.IO
{
    public abstract partial class Stream : MarshalByRefObject, IDisposable, IAsyncDisposable
    {
        public static readonly Stream Null = new NullStream();

        /// <summary>To serialize async operations on streams that don't implement their own.</summary>
        private protected SemaphoreSlim? _asyncActiveSemaphore;

        [MemberNotNull(nameof(_asyncActiveSemaphore))]
        private protected SemaphoreSlim EnsureAsyncActiveSemaphoreInitialized() =>
            // Lazily-initialize _asyncActiveSemaphore.  As we're never accessing the SemaphoreSlim's
            // WaitHandle, we don't need to worry about Disposing it in the case of a race condition.
            _asyncActiveSemaphore ??
            Interlocked.CompareExchange(ref _asyncActiveSemaphore, new SemaphoreSlim(1, 1), null) ??
            _asyncActiveSemaphore;

        public abstract bool CanRead { get; }
        public abstract bool CanWrite { get; }
        public abstract bool CanSeek { get; }
        public virtual bool CanTimeout => false;

        public abstract long Length { get; }
        public abstract long Position { get; set; }

        public virtual int ReadTimeout
        {
            get => throw new InvalidOperationException(SR.InvalidOperation_TimeoutsNotSupported);
            set => throw new InvalidOperationException(SR.InvalidOperation_TimeoutsNotSupported);
        }