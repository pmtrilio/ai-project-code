 *
 * @phpstan-import-type RegisterJsFileOptions from View
 * @phpstan-import-type RegisterCssFileOptions from View
 * @phpstan-import-type PublishOptions from AssetManager
 */
class AssetBundle extends BaseObject
{
    /**
     * @var string|null the directory that contains the source asset files for this asset bundle.
     * A source asset file is a file that is part of your source code repository of your Web application.
     *
     * You must set this property if the directory containing the source asset files is not Web accessible.
     * By setting this property, [[AssetManager]] will publish the source asset files
     * to a Web-accessible directory automatically when the asset bundle is registered on a page.
     *
     * If you do not set this property, it means the source asset files are located under [[basePath]].
     *
     * You can use either a directory or an alias of the directory.
     * @see publishOptions
     */
    public $sourcePath;
    /**
     * @var string the Web-accessible directory that contains the asset files in this bundle.
     *
     * If [[sourcePath]] is set, this property will be *overwritten* by [[AssetManager]]
     * when it publishes the asset files from [[sourcePath]].
     *
     * You can use either a directory or an alias of the directory.
     */
    public $basePath;
    /**
     * @var string the base URL for the relative asset files listed in [[js]] and [[css]].
     *
     * If [[sourcePath]] is set, this property will be *overwritten* by [[AssetManager]]
     * when it publishes the asset files from [[sourcePath]].
     *
     * You can use either a URL or an alias of the URL.
     */
    public $baseUrl;
    /**
     * @var class-string[] list of bundle class names that this bundle depends on.
     *
     * For example:
     *
     * ```
     * public $depends = [
     *    'yii\web\YiiAsset',
     *    'yii\bootstrap\BootstrapAsset',
     * ];
     * ```
     */
    public $depends = [];
    /**
     * @var (string|array<array-key, mixed>)[] list of JavaScript files that this bundle contains. Each JavaScript file can be
     * specified in one of the following formats:
     *
     * - an absolute URL representing an external asset. For example,
     *   `https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js` or
     *   `//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js`.
     * - a relative path representing a local asset (e.g. `js/main.js`). The actual file path of a local
     *   asset can be determined by prefixing [[basePath]] to the relative path, and the actual URL
     *   of the asset can be determined by prefixing [[baseUrl]] to the relative path.
     * - an array, with the first entry being the URL or relative path as described before, and a list of key => value pairs
     *   that will be used to overwrite [[jsOptions]] settings for this entry.
     *   This functionality is available since version 2.0.7.
     *
     * Note that only a forward slash "/" should be used as directory separator.
     */
    public $js = [];
    /**
     * @var (string|array<array-key, mixed>)[] list of CSS files that this bundle contains. Each CSS file can be specified
     * in one of the three formats as explained in [[js]].
     *
     * Note that only a forward slash "/" should be used as directory separator.
     */
    public $css = [];
    /**
     * @var RegisterJsFileOptions the options that will be passed to [[View::registerJsFile()]]
     * when registering the JS files in this bundle.
     */
    public $jsOptions = [];
    /**
     * @var RegisterCssFileOptions the options that will be passed to [[View::registerCssFile()]]
     * when registering the CSS files in this bundle.
     */
    public $cssOptions = [];
    /**
     * @var PublishOptions the options to be passed to [[AssetManager::publish()]] when the asset bundle
     * is being published. This property is used only when [[sourcePath]] is set.
     */
    public $publishOptions = [];


    /**
     * Registers this asset bundle with a view.
     * @param View $view the view to be registered with
     * @return static the registered asset bundle instance
     */
    public static function register($view)
    {
        /** @var static */
        $result = $view->registerAssetBundle(get_called_class());

        return $result;
    }

    /**
     * Initializes the bundle.
     * If you override this method, make sure you call the parent implementation in the last.
     */
    public function init()
    {
        if ($this->sourcePath !== null) {
            $this->sourcePath = rtrim(Yii::getAlias($this->sourcePath), '/\\');
        }
        if ($this->basePath !== null) {
            $this->basePath = rtrim(Yii::getAlias($this->basePath), '/\\');
        }
        if ($this->baseUrl !== null) {
            $this->baseUrl = rtrim(Yii::getAlias($this->baseUrl), '/');
        }