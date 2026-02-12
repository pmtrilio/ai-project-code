use Laminas\Stdlib\ArrayUtils;
use Psr\Container\ContainerInterface;

use function dirname;
use function file_exists;
use function file_put_contents;
use function get_include_path;
use function gettype;
use function is_array;
use function is_dir;
use function is_file;
use function is_object;
use function is_readable;
use function is_string;
use function is_writable;
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