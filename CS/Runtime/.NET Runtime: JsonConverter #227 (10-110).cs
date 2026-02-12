
namespace System.Text.Json.Serialization
{
    /// <summary>
    /// Converts an object or value to or from JSON.
    /// </summary>
    public abstract partial class JsonConverter
    {
        internal JsonConverter()
        {
            IsInternalConverter = GetType().Assembly == typeof(JsonConverter).Assembly;
            ConverterStrategy = GetDefaultConverterStrategy();
        }

        /// <summary>
        /// Gets the type being converted by the current converter instance.
        /// </summary>
        /// <remarks>
        /// For instances of type <see cref="JsonConverter{T}"/> returns typeof(T),
        /// and for instances of type <see cref="JsonConverterFactory"/> returns <see langword="null" />.
        /// </remarks>
        public abstract Type? Type { get; }

        /// <summary>
        /// Determines whether the type can be converted.
        /// </summary>
        /// <param name="typeToConvert">The type is checked as to whether it can be converted.</param>
        /// <returns>True if the type can be converted, false otherwise.</returns>
        public abstract bool CanConvert(Type typeToConvert);

        internal ConverterStrategy ConverterStrategy
        {
            get => _converterStrategy;
            init
            {
                CanUseDirectReadOrWrite = value == ConverterStrategy.Value && IsInternalConverter;
                RequiresReadAhead = value == ConverterStrategy.Value;
                _converterStrategy = value;
            }
        }

        private ConverterStrategy _converterStrategy;

        /// <summary>
        /// Invoked by the base contructor to populate the initial value of the <see cref="ConverterStrategy"/> property.
        /// Used for declaring the default strategy for specific converter hierarchies without explicitly setting in a constructor.
        /// </summary>
        private protected abstract ConverterStrategy GetDefaultConverterStrategy();

        /// <summary>
        /// Indicates that the converter can consume the <see cref="JsonTypeInfo.CreateObject"/> delegate.
        /// Needed because certain collection converters cannot support arbitrary delegates.
        /// TODO remove once https://github.com/dotnet/runtime/pull/73395/ and
        /// https://github.com/dotnet/runtime/issues/71944 have been addressed.
        /// </summary>
        internal virtual bool SupportsCreateObjectDelegate => false;

        /// <summary>
        /// Indicates that the converter is compatible with <see cref="JsonObjectCreationHandling.Populate"/>.
        /// </summary>
        internal virtual bool CanPopulate => false;

        /// <summary>
        /// Can direct Read or Write methods be called (for performance).
        /// </summary>
        internal bool CanUseDirectReadOrWrite { get; set; }

        /// <summary>
        /// The converter supports writing and reading metadata.
        /// </summary>
        internal virtual bool CanHaveMetadata => false;

        /// <summary>
        /// The converter supports polymorphic writes; only reserved for System.Object types.
        /// </summary>
        internal bool CanBePolymorphic { get; init; }

        /// <summary>
        /// The serializer must read ahead all contents of the next JSON value
        /// before calling into the converter for deserialization.
        /// </summary>
        internal bool RequiresReadAhead { get; private protected set; }

        /// <summary>
        /// Whether the converter is a special root-level value streaming converter.
        /// </summary>
        internal bool IsRootLevelMultiContentStreamingConverter { get; init; }

        /// <summary>
        /// Used to support JsonObject as an extension property in a loosely-typed, trimmable manner.
        /// </summary>
        internal virtual void ReadElementAndSetProperty(
            object obj,
            string propertyName,
            ref Utf8JsonReader reader,
            JsonSerializerOptions options,
            scoped ref ReadStack state)
        {
            Debug.Fail("Should not be reachable.");

            throw new InvalidOperationException();