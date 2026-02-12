
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
     * @see fullPattern
     */
    public $allowName = false;
    /**
     * @var bool whether to check whether the email's domain exists and has either an A or MX record.
     * Be aware that this check can fail due to temporary DNS problems even if the email address is
     * valid and an email would be deliverable. Defaults to false.
     */
    public $checkDNS = false;
    /**
     * @var bool whether validation process should take into account IDN (internationalized domain
     * names). Defaults to false meaning that validation of emails containing IDN will always fail.
     * Note that in order to use IDN validation you have to install and enable `intl` PHP extension,
     * otherwise an exception would be thrown.
     */
    public $enableIDN = false;
    /**
     * @var bool whether [[enableIDN]] should apply to the local part of the email (left side
     * of the `@`). Only applies if [[enableIDN]] is `true`.
     * @since 2.0.43
     */
    public $enableLocalIDN = true;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        if ($this->enableIDN && !function_exists('idn_to_ascii')) {
            throw new InvalidConfigException('In order to use IDN validation intl extension must be installed and enabled.');
        }
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is not a valid email address.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function validateValue($value)
    {
        if (!is_string($value)) {
            $valid = false;
        } elseif (!preg_match('/^(?P<name>(?:"?([^"]*)"?\s)?)(?:\s+)?(?:(?P<open><?)((?P<local>.+)@(?P<domain>[^>]+))(?P<close>>?))$/i', $value, $matches)) {
            $valid = false;
        } else {
            if ($this->enableIDN) {
                if ($this->enableLocalIDN) {
                    $matches['local'] = $this->idnToAsciiWithFallback($matches['local']);
                }
                $matches['domain'] = $this->idnToAscii($matches['domain']);
                $value = $matches['name'] . $matches['open'] . $matches['local'] . '@' . $matches['domain'] . $matches['close'];
            }

            if (strlen($matches['local']) > 64) {
                // The maximum total length of a user name or other local-part is 64 octets. RFC 5322 section 4.5.3.1.1
                // https://datatracker.ietf.org/doc/html/rfc5321#section-4.5.3.1.1
                $valid = false;