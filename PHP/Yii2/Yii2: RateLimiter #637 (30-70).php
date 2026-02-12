 * {
 *     return [
 *         'rateLimiter' => [
 *             'class' => \yii\filters\RateLimiter::class,
 *         ],
 *     ];
 * }
 * ```
 *
 * When the user has exceeded his rate limit, RateLimiter will throw a [[TooManyRequestsHttpException]] exception.
 *
 * Note that RateLimiter requires [[user]] to implement the [[RateLimitInterface]]. RateLimiter will
 * do nothing if [[user]] is not set or does not implement [[RateLimitInterface]].
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 *
 * @template T of Component
 * @extends ActionFilter<T>
 */
class RateLimiter extends ActionFilter
{
    /**
     * @var bool whether to include rate limit headers in the response
     */
    public $enableRateLimitHeaders = true;
    /**
     * @var string the message to be displayed when rate limit exceeds
     */
    public $errorMessage = 'Rate limit exceeded.';
    /**
     * @var RateLimitInterface|IdentityInterface|Closure|null the user object that implements the RateLimitInterface. If not set, it will take the value of `Yii::$app->user->getIdentity(false)`.
     * {@since 2.0.38} It's possible to provide a closure function in order to assign the user identity on runtime. Using a closure to assign the user identity is recommend
     * when you are **not** using the standard `Yii::$app->user` component. See the example below:
     * ```
     * 'user' => function() {
     *     return Yii::$app->apiUser->identity;
     * }
     * ```
     */
    public $user;