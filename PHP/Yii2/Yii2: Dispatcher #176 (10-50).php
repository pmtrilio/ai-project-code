
use Yii;
use yii\base\Component;
use yii\base\ErrorHandler;

/**
 * Dispatcher manages a set of [[Target|log targets]].
 *
 * Dispatcher implements the [[dispatch()]]-method that forwards the log messages from a [[Logger]] to
 * the registered log [[targets]].
 *
 * An instance of Dispatcher is registered as a core application component and can be accessed using `Yii::$app->log`.
 *
 * You may configure the targets in application configuration, like the following:
 *
 * ```
 * [
 *     'components' => [
 *         'log' => [
 *             'targets' => [
 *                 'file' => [
 *                     'class' => 'yii\log\FileTarget',
 *                     'levels' => ['trace', 'info'],
 *                     'categories' => ['yii\*'],
 *                 ],
 *                 'email' => [
 *                     'class' => 'yii\log\EmailTarget',
 *                     'levels' => ['error', 'warning'],
 *                     'message' => [
 *                         'to' => 'admin@example.com',
 *                     ],
 *                 ],
 *             ],
 *         ],
 *     ],
 * ]
 * ```
 *
 * Each log target can have a name and can be referenced via the [[targets]] property as follows:
 *
 * ```