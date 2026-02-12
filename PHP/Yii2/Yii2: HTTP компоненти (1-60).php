<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\base;

use Yii;

/**
 * Application is the base class for all application classes.
 *
 * For more details and usage information on Application, see the [guide article on applications](guide:structure-applications).
 *
 * @property-read \yii\web\AssetManager $assetManager The asset manager application component.
 * @property-read \yii\rbac\ManagerInterface|null $authManager The auth manager application component or null
 * if it's not configured.
 * @property string $basePath The root directory of the application.
 * @property-read \yii\caching\CacheInterface|null $cache The cache application component. Null if the
 * component is not enabled.
 * @property-write array $container Values given in terms of name-value pairs.
 * @property-read \yii\db\Connection $db The database connection.
 * @property-read \yii\web\ErrorHandler|\yii\console\ErrorHandler $errorHandler The error handler application
 * component.
 * @property-read \yii\i18n\Formatter $formatter The formatter application component.
 * @property-read \yii\i18n\I18N $i18n The internationalization application component.
 * @property-read \yii\log\Dispatcher $log The log dispatcher application component.
 * @property-read \yii\mail\MailerInterface $mailer The mailer application component.
 * @property-read \yii\web\Request|\yii\console\Request $request The request component.
 * @property-read \yii\web\Response|\yii\console\Response $response The response component.
 * @property string $runtimePath The directory that stores runtime files. Defaults to the "runtime"
 * subdirectory under [[basePath]].
 * @property-read \yii\base\Security $security The security application component.
 * @property string $timeZone The time zone used by this application.
 * @property-read string $uniqueId The unique ID of the module.
 * @property-read \yii\web\UrlManager $urlManager The URL manager for this application.
 * @property string $vendorPath The directory that stores vendor files. Defaults to "vendor" directory under
 * [[basePath]].
 * @property-read View|\yii\web\View $view The view application component that is used to render various view
 * files.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class Application extends Module
{
    /**
     * @event Event an event raised before the application starts to handle a request.
     */
    public const EVENT_BEFORE_REQUEST = 'beforeRequest';
    /**
     * @event Event an event raised after the application successfully handles a request (before the response is sent out).
     */
    public const EVENT_AFTER_REQUEST = 'afterRequest';
    /**
     * Application state used by [[state]]: application just started.
     */