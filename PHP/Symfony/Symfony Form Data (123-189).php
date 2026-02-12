    /**
     * Returns the normalized data of the field, used as internal bridge
     * between model data and view data.
     *
     * @return mixed When the field is not submitted, the default data is returned.
     *               When the field is submitted, the normalized submitted data
     *               is returned if the field is synchronized with the view data,
     *               null otherwise.
     *
     * @throws Exception\RuntimeException If the form inherits data but has no parent
     */
    public function getNormData(): mixed;

    /**
     * Returns the view data of the field.
     *
     * It may be defined by {@link FormConfigInterface::getDataClass}.
     *
     * There are two cases:
     *
     * - When the form is compound the view data is mapped to the children.
     *   Each child will use its mapped data as model data.
     *   It can be an array, an object or null.
     *
     * - When the form is simple its view data is used to be bound
     *   to the submitted data.
     *   It can be a string or an array.
     *
     * In both cases the view data is the actual altered data on submission.
     *
     * @throws Exception\RuntimeException If the form inherits data but has no parent
     */
    public function getViewData(): mixed;

    /**
     * Returns the extra submitted data.
     *
     * @return array The submitted data which do not belong to a child
     */
    public function getExtraData(): array;

    /**
     * Returns the form's configuration.
     */
    public function getConfig(): FormConfigInterface;

    /**
     * Returns whether the form is submitted.
     */
    public function isSubmitted(): bool;

    /**
     * Returns the name by which the form is identified in forms.
     *
     * Only root forms are allowed to have an empty name.
     */
    public function getName(): string;

    /**
     * Returns the property path that the form is mapped to.
     */
    public function getPropertyPath(): ?PropertyPathInterface;

    /**
     * Adds an error to this form.
     *
     * @return $this