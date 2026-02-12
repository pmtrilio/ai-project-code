 * to provide client-side validation support.
 *
 * Validator declares a set of [[builtInValidators|built-in validators]] which can
 * be referenced using short names. They are listed as follows:
 *
 * - `boolean`: [[BooleanValidator]]
 * - `captcha`: [[\yii\captcha\CaptchaValidator]]
 * - `compare`: [[CompareValidator]]
 * - `date`: [[DateValidator]]
 * - `datetime`: [[DateValidator]]
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