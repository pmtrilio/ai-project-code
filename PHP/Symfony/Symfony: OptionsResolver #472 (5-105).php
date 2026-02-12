 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\OptionsResolver;

use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\Exception\OptionDefinitionException;
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
     */
    private array $normalizers = [];

    /**
     * A list of accepted values for each option.
     */
    private array $allowedValues = [];

    /**
     * A list of accepted types for each option.
     */
    private array $allowedTypes = [];

    /**
     * A list of info messages for each option.
     */
    private array $info = [];

    /**
     * A list of closures for evaluating lazy options.
     */
    private array $lazy = [];

    /**
     * A list of lazy options whose closure is currently being called.