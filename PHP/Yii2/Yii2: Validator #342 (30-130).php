 * - `time`: [[DateValidator]]
 * - `default`: [[DefaultValueValidator]]
 * - `double`: [[NumberValidator]]
 * - `each`: [[EachValidator]]
 * - `email`: [[EmailValidator]]
 * - `exist`: [[ExistValidator]]
 * - `file`: [[FileValidator]]
 * - `filter`: [[FilterValidator]]
 * - `image`: [[ImageValidator]]
 * - `in`: [[RangeValidator]]
 * - `integer`: [[NumberValidator]]
 * - `match`: [[RegularExpressionValidator]]
 * - `required`: [[RequiredValidator]]
 * - `safe`: [[SafeValidator]]
 * - `string`: [[StringValidator]]
 * - `trim`: [[TrimValidator]]
 * - `unique`: [[UniqueValidator]]
 * - `url`: [[UrlValidator]]
 * - `ip`: [[IpValidator]]
 *
 * For more details and usage information on Validator, see the [guide article on validators](guide:input-validation).
 *
 * @property-read array $attributeNames Attribute names.
 * @property-read array|null $validationAttributes List of attribute names.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Validator extends Component
{
    /**
     * @var array list of built-in validators (name => class or configuration)
     */
    public static $builtInValidators = [
        'boolean' => 'yii\validators\BooleanValidator',
        'captcha' => 'yii\captcha\CaptchaValidator',
        'compare' => 'yii\validators\CompareValidator',
        'date' => 'yii\validators\DateValidator',
        'datetime' => [
            'class' => 'yii\validators\DateValidator',
            'type' => DateValidator::TYPE_DATETIME,
        ],
        'time' => [
            'class' => 'yii\validators\DateValidator',
            'type' => DateValidator::TYPE_TIME,
        ],
        'default' => 'yii\validators\DefaultValueValidator',
        'double' => 'yii\validators\NumberValidator',
        'each' => 'yii\validators\EachValidator',
        'email' => 'yii\validators\EmailValidator',
        'exist' => 'yii\validators\ExistValidator',
        'file' => 'yii\validators\FileValidator',
        'filter' => 'yii\validators\FilterValidator',
        'image' => 'yii\validators\ImageValidator',
        'in' => 'yii\validators\RangeValidator',
        'integer' => [
            'class' => 'yii\validators\NumberValidator',
            'integerOnly' => true,
        ],
        'match' => 'yii\validators\RegularExpressionValidator',
        'number' => 'yii\validators\NumberValidator',
        'required' => 'yii\validators\RequiredValidator',
        'safe' => 'yii\validators\SafeValidator',
        'string' => 'yii\validators\StringValidator',
        'trim' => [
            'class' => 'yii\validators\TrimValidator',
            'skipOnArray' => true,
        ],
        'unique' => 'yii\validators\UniqueValidator',
        'url' => 'yii\validators\UrlValidator',
        'ip' => 'yii\validators\IpValidator',
    ];
    /**
     * @var array|string attributes to be validated by this validator. For multiple attributes,
     * please specify them as an array; for single attribute, you may use either a string or an array.
     */
    public $attributes = [];
    /**
     * @var string|null the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * Note that some validators may introduce other properties for error messages used when specific
     * validation conditions are not met. Please refer to individual class API documentation for details
     * about these properties. By convention, this property represents the primary error message
     * used when the most important validation condition is not met.
     */
    public $message;
    /**
     * @var array|string scenarios that the validator can be applied to. For multiple scenarios,
     * please specify them as an array; for single scenario, you may use either a string or an array.
     */
    public $on = [];
    /**
     * @var array|string scenarios that the validator should not be applied to. For multiple scenarios,
     * please specify them as an array; for single scenario, you may use either a string or an array.
     */
    public $except = [];
    /**