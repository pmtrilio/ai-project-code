     */
    protected $attributes = [];

    /**
     * Create a new fluent instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     */
    public function __construct($attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Create a new fluent instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return static
     */
    public static function make($attributes = [])
    {
        return new static($attributes);
    }

    /**
     * Get an attribute from the fluent instance using "dot" notation.
     *
     * @template TGetDefault
     *
     * @param  TKey  $key
     * @param  TGetDefault|(\Closure(): TGetDefault)  $default
     * @return TValue|TGetDefault
     */
    public function get($key, $default = null)
    {
        return data_get($this->attributes, $key, $default);
    }

    /**
     * Set an attribute on the fluent instance using "dot" notation.
     *
     * @param  TKey  $key
     * @param  TValue  $value
     * @return $this
     */
    public function set($key, $value)
    {
        data_set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * Fill the fluent instance with an array of attributes.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {