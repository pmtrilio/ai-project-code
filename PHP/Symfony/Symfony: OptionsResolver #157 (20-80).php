use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

/**
 * Validates options and merges them with default values.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class OptionsResolver implements Options
{
    private const VALIDATION_FUNCTIONS = [
        'bool' => 'is_bool',
        'boolean' => 'is_bool',
        'int' => 'is_int',
        'integer' => 'is_int',
        'long' => 'is_int',
        'float' => 'is_float',
        'double' => 'is_float',
        'real' => 'is_float',
        'numeric' => 'is_numeric',
        'string' => 'is_string',
        'scalar' => 'is_scalar',
        'array' => 'is_array',
        'iterable' => 'is_iterable',
        'countable' => 'is_countable',
        'callable' => 'is_callable',
        'object' => 'is_object',
        'resource' => 'is_resource',
    ];

    /**
     * The names of all defined options.
     */
    private array $defined = [];

    /**
     * The default option values.
     */
    private array $defaults = [];

    /**
     * A list of closure for nested options.
     *
     * @var \Closure[][]
     */
    private array $nested = [];

    /**
     * The names of required options.
     */
    private array $required = [];

    /**
     * The resolved option values.
     */
    private array $resolved = [];

    /**
     * A list of normalizer closures.
     *
     * @var \Closure[][]