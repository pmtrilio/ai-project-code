use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\ErrorHandler\DebugClassLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\AddAnnotatedClassesToCachePass;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;

// Help opcache.preload discover always-needed symbols
class_exists(ConfigCache::class);

/**
 * The Kernel is the heart of the Symfony system.
 *
 * It manages an environment made of bundles.
 *
 * Environment names must always start with a letter and
 * they must only contain letters and numbers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Kernel implements KernelInterface, RebootableInterface, TerminableInterface
{
    /**
     * @var array<string, BundleInterface>
     */
    protected $bundles = [];

    protected $container;
    protected $environment;
    protected $debug;
    protected $booted = false;
    protected $startTime;

    private string $projectDir;
    private ?string $warmupDir = null;