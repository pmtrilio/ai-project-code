    /// <param name="metadataProvider">
    /// <see cref="IModelMetadataProvider"/> instance used to create <see cref="ViewFeatures.ModelExplorer"/>
    /// instances.
    /// </param>
    /// <param name="modelState"><see cref="ModelStateDictionary"/> instance for this scope.</param>
    /// <remarks>For use when creating a <see cref="ViewDataDictionary"/> for a new top-level scope.</remarks>
    public ViewDataDictionary(
        IModelMetadataProvider metadataProvider,
        ModelStateDictionary modelState)
        : this(metadataProvider, modelState, declaredModelType: typeof(object))
    {
    }

    /// <summary>
    /// Initializes a new instance of the <see cref="ViewDataDictionary"/> class based entirely on an existing
    /// instance.
    /// </summary>
    /// <param name="source"><see cref="ViewDataDictionary"/> instance to copy initial values from.</param>
    /// <remarks>
    /// <para>
    /// For use when copying a <see cref="ViewDataDictionary"/> instance and the declared <see cref="Model"/>
    /// <see cref="Type"/> will not change e.g. when copying from a <see cref="ViewDataDictionary{TModel}"/>
    /// instance to a base <see cref="ViewDataDictionary"/> instance.
    /// </para>
    /// <para>
    /// This constructor should not be used in any context where <see cref="Model"/> may be set to a value
    /// incompatible with the declared type of <paramref name="source"/>.
    /// </para>
    /// </remarks>
    public ViewDataDictionary(ViewDataDictionary source)
        : this(source, source.Model, source._declaredModelType)
    {
    }

    /// <summary>
    /// Initializes a new instance of the <see cref="ViewDataDictionary"/> class.
    /// </summary>
    /// <param name="metadataProvider">
    /// <see cref="IModelMetadataProvider"/> instance used to create <see cref="ViewFeatures.ModelExplorer"/>
    /// instances.
    /// </param>