 *
 * @phpstan-immutable
 */
class Time extends ChronosTime implements JsonSerializable, Stringable
{
    use DateFormatTrait;

    /**
     * The format to use when formatting a time using `Cake\I18n\Time::i18nFormat()`
     * and `__toString`.
     *
     * The format should be either the formatting constants from IntlDateFormatter as
     * described in (https://secure.php.net/manual/en/class.intldateformatter.php) or a pattern
     * as specified in (https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details)
     *
     * @var string|int
     * @see \Cake\I18n\Time::i18nFormat()
     */
    protected static string|int $_toStringFormat = IntlDateFormatter::SHORT;

    /**
     * The format to use when converting this object to JSON.
     *
     * The format should be either the formatting constants from IntlDateFormatter as
     * described in (https://secure.php.net/manual/en/class.intldateformatter.php) or a pattern
     * as specified in (https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details)
     *
     * @var \Closure|string|int
     * @see \Cake\I18n\Date::i18nFormat()
     */
    protected static Closure|string|int $_jsonEncodeFormat = "HH':'mm':'ss";

    /**
     * The format to use when formatting a time using `Cake\I18n\Time::nice()`
     *
     * The format should be either the formatting constants from IntlDateFormatter as
     * described in (https://secure.php.net/manual/en/class.intldateformatter.php) or a pattern
     * as specified in (https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details)
     *
     * @var string|int
     * @see \Cake\I18n\Time::nice()