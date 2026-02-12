class Fluent implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    use Conditionable, InteractsWithData, Macroable {
        __call as macroCall;
    }

    /**
     * All of the attributes set on the fluent instance.
     *
     * @var array<TKey, TValue>
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