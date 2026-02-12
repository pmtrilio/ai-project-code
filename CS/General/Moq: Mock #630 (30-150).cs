        /// </summary>
        protected Mock()
        {
        }

        /// <summary>
        ///   Retrieves the mock object for the given object instance.
        /// </summary>
        /// <param name="mocked">The instance of the mocked object.</param>
        /// <typeparam name="T">
        ///   Type of the mock to retrieve.
        ///   Can be omitted as it's inferred from the object instance passed in as the <paramref name="mocked"/> instance.
        /// </typeparam>
        /// <returns>The mock associated with the mocked object.</returns>
        /// <exception cref="ArgumentException">The received <paramref name="mocked"/> instance was not created by Moq.</exception>
        /// <example group="advanced">
        ///   The following example shows how to add a new setup to an object instance
        ///   which is not the original <see cref="Mock{T}"/> but rather the object associated with it:
        ///   <code>
        ///     // Typed instance, not the mock, is retrieved from some test API.
        ///     HttpContextBase context = GetMockContext();
        ///
        ///     // context.Request is the typed object from the "real" API
        ///     // so in order to add a setup to it, we need to get
        ///     // the mock that "owns" it
        ///     Mock&lt;HttpRequestBase&gt; request = Mock.Get(context.Request);
        ///
        ///     request.Setup(req => req.AppRelativeCurrentExecutionFilePath)
        ///            .Returns(tempUrl);
        ///   </code>
        /// </example>
        public static Mock<T> Get<T>(T mocked) where T : class
        {
            if (mocked is IMocked<T> mockedOfT)
            {
                // This would be the fastest check.
                return mockedOfT.Mock;
            }

            if (mocked is Delegate aDelegate && aDelegate.Target is IMocked<T> mockedDelegateImpl)
            {
                return mockedDelegateImpl.Mock;
            }

            if (mocked is IMocked mockedPlain)
            {
                // We may have received a T of an implemented 
                // interface in the mock.
                var mock = mockedPlain.Mock;
                if (mock.ImplementsInterface(typeof(T)))
                {
                    return mock.As<T>();
                }

                // Alternatively, we may have been asked 
                // for a type that is assignable to the 
                // one for the mock.
                // This is not valid as generic types 
                // do not support covariance on 
                // the generic parameters.
                var imockedType = mocked.GetType().GetInterfaces().Single(i => i.Name.Equals("IMocked`1", StringComparison.Ordinal));
                var mockedType = imockedType.GetGenericArguments()[0];
                var types = string.Join(
                    ", ",
                    new[] { mockedType }
                        // Ignore internally defined IMocked<T>
                        .Concat(mock.InheritedInterfaces)
                        .Concat(mock.AdditionalInterfaces)
                        .Select(t => t.Name)
                        .ToArray());

                throw new ArgumentException(string.Format(
                    CultureInfo.CurrentCulture,
                    Resources.InvalidMockGetType,
                    typeof(T).Name,
                    types));
            }

            throw new ArgumentException(Resources.ObjectInstanceNotMock, "mocked");
        }

        /// <summary>
        ///   Verifies that all verifiable expectations have been met.
        /// </summary>
        /// <exception cref="MockException">Not all verifiable expectations were met.</exception>
        public static void Verify(params Mock[] mocks)
        {
            foreach (var mock in mocks)
            {
                mock.Verify();
            }
        }

        /// <summary>
        ///   Verifies all expectations regardless of whether they have been flagged as verifiable.
        /// </summary>
        /// <exception cref="MockException">At least one expectation was not met.</exception>
        public static void VerifyAll(params Mock[] mocks)
        {
            foreach (var mock in mocks)
            {
                mock.VerifyAll();
            }
        }

        /// <summary>
        /// Gets the interfaces additionally implemented by the mock object.
        /// </summary>
        /// <remarks>
        /// This list may be modified by calls to <see cref="As{TInterface}"/> up until the first call to <see cref="Object"/>.
        /// </remarks>
        internal abstract List<Type> AdditionalInterfaces { get; }

        /// <summary>
        ///   Behavior of the mock, according to the value set in the constructor.
        /// </summary>
        public abstract MockBehavior Behavior { get; }

        /// <summary>
        ///   Whether the base member virtual implementation will be called for mocked classes if no setup is matched.
        ///   Defaults to <see langword="false"/>.