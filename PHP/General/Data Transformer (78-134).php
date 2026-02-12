     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as NULL). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param TTransformedValue|null $value The value in the transformed representation
     *
     * @return TValue|null
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function reverseTransform(mixed $value): mixed;
}
