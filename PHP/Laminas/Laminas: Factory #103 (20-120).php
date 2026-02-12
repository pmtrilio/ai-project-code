use function pathinfo;
use function sprintf;
use function stream_resolve_include_path;
use function strrchr;
use function strtolower;
use function substr;

class Factory
{
    /**
     * Plugin manager for loading readers
     *
     * @var null|ContainerInterface
     */
    public static $readers;

    /**
     * Plugin manager for loading writers
     *
     * @var null|ContainerInterface
     */
    public static $writers;

    /**
     * Registered config file extensions.
     * key is extension, value is reader instance or plugin name
     *
     * @var array
     */
    protected static $extensions = [
        'ini'        => 'ini',
        'json'       => 'json',
        'xml'        => 'xml',
        'yaml'       => 'yaml',
        'yml'        => 'yaml',
        'properties' => 'javaproperties',
    ];

    /**
     * Register config file extensions for writing
     * key is extension, value is writer instance or plugin name
     *
     * @var array
     */
    protected static $writerExtensions = [
        'php'  => 'php',
        'ini'  => 'ini',
        'json' => 'json',
        'xml'  => 'xml',
        'yaml' => 'yaml',
        'yml'  => 'yaml',
    ];

    /**
     * Read a config from a file.
     *
     * @param  string  $filename
     * @param  bool $returnConfigObject
     * @param  bool $useIncludePath
     * @return array|Config
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public static function fromFile($filename, $returnConfigObject = false, $useIncludePath = false)
    {
        $filepath = $filename;
        if (! file_exists($filename)) {
            if (! $useIncludePath) {
                throw new Exception\RuntimeException(sprintf(
                    'Filename "%s" cannot be found relative to the working directory',
                    $filename
                ));
            }

            $fromIncludePath = stream_resolve_include_path($filename);
            if (! $fromIncludePath) {
                throw new Exception\RuntimeException(sprintf(
                    'Filename "%s" cannot be found relative to the working directory or the include_path ("%s")',
                    $filename,
                    get_include_path()
                ));
            }
            $filepath = $fromIncludePath;
        }

        $pathinfo = pathinfo($filepath);

        if (! isset($pathinfo['extension'])) {
            throw new Exception\RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }

        $extension = strtolower($pathinfo['extension']);

        if ($extension === 'php') {
            if (! is_file($filepath) || ! is_readable($filepath)) {
                throw new Exception\RuntimeException(sprintf(
                    "File '%s' doesn't exist or not readable",
                    $filename