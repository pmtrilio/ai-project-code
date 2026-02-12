    ///   Base class for mocks and static helper class with methods that apply to mocked objects,
    ///   such as <see cref="Get"/> to retrieve a <see cref="Mock{T}"/> from an object instance.
    /// </summary>
    public abstract partial class Mock : IFluentInterface
    {
        internal static readonly MethodInfo GetMethod =
            typeof(Mock).GetMethod(nameof(Get), BindingFlags.Public | BindingFlags.Static);

        /// <summary>
        ///   Initializes a new instance of the <see cref="Mock"/> class.
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
