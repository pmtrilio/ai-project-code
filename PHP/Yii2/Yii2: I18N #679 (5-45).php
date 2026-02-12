 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\i18n;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * I18N provides features related with internationalization (I18N) and localization (L10N).
 *
 * I18N is configured as an application component in [[\yii\base\Application]] by default.
 * You can access that instance via `Yii::$app->i18n`.
 *
 * @property MessageFormatter $messageFormatter The message formatter to be used to format message via ICU
 * message format. Note that the type of this property differs in getter and setter. See
 * [[getMessageFormatter()]] and [[setMessageFormatter()]] for details.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class I18N extends Component
{
    /**
     * @var array list of [[MessageSource]] configurations or objects. The array keys are message
     * category patterns, and the array values are the corresponding [[MessageSource]] objects or the configurations
     * for creating the [[MessageSource]] objects.
     *
     * The message category patterns can contain the wildcard `*` at the end to match multiple categories with the same prefix.
     * For example, `app/*` matches both `app/cat1` and `app/cat2`.
     *
     * The `*` category pattern will match all categories that do not match any other category patterns.
     *
     * This property may be modified on the fly by extensions who want to have their own message sources
     * registered under their own namespaces.
     *
     * The category `yii` and `app` are always defined. The former refers to the messages used in the Yii core
     * framework code, while the latter refers to the default message category for custom application code.
     * By default, both of these categories use [[PhpMessageSource]] and the corresponding message files are