 *   (this is best used in stateless RESTful API implementation).
 *
 * Note that User only maintains the user authentication status. It does NOT handle how to authenticate
 * a user. The logic of how to authenticate a user should be done in the class implementing [[IdentityInterface]].
 * You are also required to set [[identityClass]] with the name of this class.
 *
 * User is configured as an application component in [[\yii\web\Application]] by default.
 * You can access that instance via `Yii::$app->user`.
 *
 * You can modify its configuration by adding an array to your application config under `components`
 * as it is shown in the following example:
 *
 * ```
 * 'user' => [
 *     'identityClass' => 'app\models\User', // User must implement the IdentityInterface
 *     'enableAutoLogin' => true,
 *     // 'loginUrl' => ['user/login'],
 *     // ...
 * ]
 * ```
 *
 * @template T of IdentityInterface
 *
 * @property-read string|int|null $id The unique identifier for the user. If `null`, it means the user is a
 * guest.
 * @property T|null $identity The identity object associated with the currently logged-in user. `null` is
 * returned if the user is not logged in (not authenticated).
 * @property-read bool $isGuest Whether the current user is a guest.
 * @property string $returnUrl The URL that the user should be redirected to after login. Note that the type
 * of this property differs in getter and setter. See [[getReturnUrl()]] and [[setReturnUrl()]] for details.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class User extends Component
{
    public const EVENT_BEFORE_LOGIN = 'beforeLogin';
    public const EVENT_AFTER_LOGIN = 'afterLogin';
    public const EVENT_BEFORE_LOGOUT = 'beforeLogout';
    public const EVENT_AFTER_LOGOUT = 'afterLogout';
    /**