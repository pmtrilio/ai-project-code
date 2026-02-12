
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * EmailValidator validates that the attribute value is a valid email address.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class EmailValidator extends Validator
{
    /**
     * @var string the regular expression used to validate the attribute value.
     * @see https://www.regular-expressions.info/email.html
     */
    public $pattern = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
    /**
     * @var string the regular expression used to validate email addresses with the name part.
     * This property is used only when [[allowName]] is true.
     * @see allowName
     */
    public $fullPattern = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?>$/';
    /**
     * @var string the regular expression used to validate the part before the @ symbol, used if ASCII conversion fails to validate the address.
     * @see https://www.regular-expressions.info/email.html
     * @since 2.0.42
     */
    public $patternASCII = '/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*$/';
    /**
     * @var string the regular expression used to validate email addresses with the name part before the @ symbol, used if ASCII conversion fails to validate the address.
     * This property is used only when [[allowName]] is true.
     * @see allowName
     * @since 2.0.42
     */
    public $fullPatternASCII = '/^[^@]*<[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*$/';
    /**
     * @var bool whether to allow name in the email address (e.g. "John Smith <john.smith@example.com>"). Defaults to false.