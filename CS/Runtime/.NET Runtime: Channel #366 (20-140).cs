        {
            ArgumentNullException.ThrowIfNull(options);

            if (options.SingleReader)
            {
                return new SingleConsumerUnboundedChannel<T>(!options.AllowSynchronousContinuations);
            }

            return new UnboundedChannel<T>(!options.AllowSynchronousContinuations);
        }

        /// <summary>Creates a channel with the specified maximum capacity.</summary>
        /// <typeparam name="T">Specifies the type of data in the channel.</typeparam>
        /// <param name="capacity">The maximum number of items the channel may store.</param>
        /// <returns>The created channel.</returns>
        /// <remarks>
        /// Channels created with this method apply the <see cref="BoundedChannelFullMode.Wait"/>
        /// behavior and prohibit continuations from running synchronously.
        /// </remarks>
        /// <exception cref="ArgumentOutOfRangeException"><paramref name="capacity"/> is negative.</exception>
        public static Channel<T> CreateBounded<T>(int capacity) =>
            capacity > 0 ? new BoundedChannel<T>(capacity, BoundedChannelFullMode.Wait, runContinuationsAsynchronously: true, itemDropped: null) :
            capacity == 0 ? new RendezvousChannel<T>(BoundedChannelFullMode.Wait, runContinuationsAsynchronously: true, itemDropped: null) :
            throw new ArgumentOutOfRangeException(nameof(capacity));

        /// <summary>Creates a channel subject to the provided options.</summary>
        /// <typeparam name="T">Specifies the type of data in the channel.</typeparam>
        /// <param name="options">Options that guide the behavior of the channel.</param>
        /// <returns>The created channel.</returns>
        /// <exception cref="ArgumentNullException"><paramref name="options"/> is <see langword="null"/>.</exception>
        public static Channel<T> CreateBounded<T>(BoundedChannelOptions options) =>
            CreateBounded<T>(options, itemDropped: null);

        /// <summary>Creates a channel subject to the provided options.</summary>
        /// <typeparam name="T">Specifies the type of data in the channel.</typeparam>
        /// <param name="options">Options that guide the behavior of the channel.</param>
        /// <param name="itemDropped">Delegate that will be called when item is being dropped from channel. See <see cref="BoundedChannelFullMode"/>.</param>
        /// <returns>The created channel.</returns>
        /// <exception cref="ArgumentNullException"><paramref name="options"/> is <see langword="null"/>.</exception>
        public static Channel<T> CreateBounded<T>(BoundedChannelOptions options, Action<T>? itemDropped)
        {
            ArgumentNullException.ThrowIfNull(options);

            return
                options.Capacity > 0 ? new BoundedChannel<T>(options.Capacity, options.FullMode, !options.AllowSynchronousContinuations, itemDropped) :
                new RendezvousChannel<T>(options.FullMode, !options.AllowSynchronousContinuations, itemDropped);
        }
    }
}
