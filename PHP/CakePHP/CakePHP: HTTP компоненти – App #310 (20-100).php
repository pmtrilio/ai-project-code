
/**
 * App is responsible for resource location, and path management.
 *
 * ### Adding paths
 *
 * Additional paths for Templates and Plugins are configured with Configure now. See config/app.php for an
 * example. The `App.paths.plugins` and `App.paths.templates` variables are used to configure paths for plugins
 * and templates respectively. All class based resources should be mapped using your application's autoloader.
 *
 * ### Inspecting loaded paths
 *
 * You can inspect the currently loaded paths using `App::classPath('Controller')` for example to see loaded
 * controller paths.
 *
 * It is also possible to inspect paths for plugin classes, for instance, to get
 * the path to a plugin's helpers you would call `App::classPath('View/Helper', 'MyPlugin')`
 *
 * ### Locating plugins
 *
 * Plugins can be located with App as well. Using Plugin::path('DebugKit') for example, will
 * give you the full path to the DebugKit plugin.
 *
 * @link https://book.cakephp.org/5/en/core-libraries/app.html
 */
class App
{
    /**
     * Return the class name namespaced. This method checks if the class is defined on the
     * application/plugin, otherwise try to load from the CakePHP core
     *
     * @param string $class Class name
     * @param string $type Type of class
     * @param string $suffix Class name suffix
     * @return class-string|null Namespaced class name, null if the class is not found.
     */
    public static function className(string $class, string $type = '', string $suffix = ''): ?string
    {
        if (str_contains($class, '\\')) {
            return class_exists($class) ? $class : null;
        }

        [$plugin, $name] = pluginSplit($class);
        $fullname = '\\' . str_replace('/', '\\', $type . '\\' . $name) . $suffix;

        $base = $plugin ?: Configure::read('App.namespace');
        if ($base !== null) {
            $base = str_replace('/', '\\', rtrim($base, '\\'));

            if (static::_classExistsInBase($fullname, $base)) {
                /** @var class-string */
                return $base . $fullname;
            }
        }

        if ($plugin || !static::_classExistsInBase($fullname, 'Cake')) {
            return null;
        }

        /** @var class-string */
        return 'Cake' . $fullname;
    }

    /**
     * Returns the plugin split name of a class
     *
     * Examples:
     *
     * ```
     * App::shortName(
     *     'SomeVendor\SomePlugin\Controller\Component\TestComponent',
     *     'Controller/Component',
     *     'Component'
     * )
     * ```
     *
     * Returns: SomeVendor/SomePlugin.Test
     *
     * ```
     * App::shortName(
     *     'SomeVendor\SomePlugin\Controller\Component\Subfolder\TestComponent',