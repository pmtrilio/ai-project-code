
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

        public virtual int WriteTimeout
        {
            get => throw new InvalidOperationException(SR.InvalidOperation_TimeoutsNotSupported);
            set => throw new InvalidOperationException(SR.InvalidOperation_TimeoutsNotSupported);
        }

        public void CopyTo(Stream destination) => CopyTo(destination, GetCopyBufferSize());

        public virtual void CopyTo(Stream destination, int bufferSize)
        {
            ValidateCopyToArguments(destination, bufferSize);
            if (!CanRead)
            {
                if (CanWrite)
                {
                    ThrowHelper.ThrowNotSupportedException_UnreadableStream();
                }

                ThrowHelper.ThrowObjectDisposedException_StreamClosed(GetType().Name);
            }

            byte[] buffer = ArrayPool<byte>.Shared.Rent(bufferSize);
            try
            {
                int bytesRead;
                while ((bytesRead = Read(buffer, 0, buffer.Length)) != 0)
                {
                    destination.Write(buffer, 0, bytesRead);
                }
            }
            finally
            {
                ArrayPool<byte>.Shared.Return(buffer);
            }
        }

        public Task CopyToAsync(Stream destination) => CopyToAsync(destination, GetCopyBufferSize());
