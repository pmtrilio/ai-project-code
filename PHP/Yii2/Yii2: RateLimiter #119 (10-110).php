
use Closure;
use Yii;
use yii\base\Action;
use yii\base\ActionFilter;
use yii\base\Component;
use yii\base\Controller;
use yii\base\Module;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\TooManyRequestsHttpException;

/**
 * RateLimiter implements a rate limiting algorithm based on the [leaky bucket algorithm](https://en.wikipedia.org/wiki/Leaky_bucket).
 *
 * You may use RateLimiter by attaching it as a behavior to a controller or module, like the following,
 *
 * ```
 * public function behaviors()
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
    /**
     * @var Request|null the current request. If not set, the `request` application component will be used.
     */
    public $request;
    /**
     * @var Response|null the response to be sent. If not set, the `response` application component will be used.
     */
    public $response;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
        if ($this->response === null) {
            $this->response = Yii::$app->getResponse();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if ($this->user === null && Yii::$app->getUser()) {
            $this->user = Yii::$app->getUser()->getIdentity(false);
        }

        if ($this->user instanceof Closure) {
            $this->user = call_user_func($this->user, $action);
        }

        if ($this->user instanceof RateLimitInterface) {
            Yii::debug('Check rate limit', __METHOD__);
            $this->checkRateLimit($this->user, $this->request, $this->response, $action);
        } elseif ($this->user) {