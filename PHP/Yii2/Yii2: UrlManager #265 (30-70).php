 *     'rules' => [
 *         // your rules go here
 *     ],
 *     // ...
 * ]
 * ```
 *
 * Rules are classes implementing the [[UrlRuleInterface]], by default that is [[UrlRule]].
 * For nesting rules, there is also a [[GroupUrlRule]] class.
 *
 * For more details and usage information on UrlManager, see the [guide article on routing](guide:runtime-routing).
 *
 * @property string $baseUrl The base URL that is used by [[createUrl()]] to prepend to created URLs.
 * @property string $hostInfo The host info (e.g. `https://www.example.com`) that is used by
 * [[createAbsoluteUrl()]] to prepend to created URLs.
 * @property string $scriptUrl The entry script URL that is used by [[createUrl()]] to prepend to created
 * URLs.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class UrlManager extends Component
{
    /**
     * @var bool whether to enable pretty URLs. Instead of putting all parameters in the query
     * string part of a URL, pretty URLs allow using path info to represent some of the parameters
     * and can thus produce more user-friendly URLs, such as "/news/Yii-is-released", instead of
     * "/index.php?r=news%2Fview&id=100".
     */
    public $enablePrettyUrl = false;
    /**
     * @var bool whether to enable strict parsing. If strict parsing is enabled, the incoming
     * requested URL must match at least one of the [[rules]] in order to be treated as a valid request.
     * Otherwise, the path info part of the request will be treated as the requested route.
     * This property is used only when [[enablePrettyUrl]] is `true`.
     */
    public $enableStrictParsing = false;
    /**
     * @var array the rules for creating and parsing URLs when [[enablePrettyUrl]] is `true`.
     * This property is used only if [[enablePrettyUrl]] is `true`. Each element in the array
     * is the configuration array for creating a single URL rule. The configuration will