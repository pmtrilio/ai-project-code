namespace Laminas\ServiceManager;

use Exception;
use Laminas\ServiceManager\Exception\ContainerModificationsNotAllowedException;
use Laminas\ServiceManager\Exception\CyclicAliasException;
use Laminas\ServiceManager\Exception\InvalidArgumentException;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Exception\ServiceNotFoundException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\Initializer\InitializerInterface;
use Laminas\ServiceManager\Proxy\LazyServiceFactory;
use Laminas\Stdlib\ArrayUtils;
use ProxyManager\Configuration as ProxyConfiguration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\GeneratorStrategy\FileWriterGeneratorStrategy;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_intersect;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function class_exists;
use function in_array;
use function is_array;
use function is_callable;
use function is_string;
use function spl_autoload_register;
use function spl_object_hash;
use function sprintf;

/**
 * Service Manager.
 *
 * Default implementation of the ServiceLocatorInterface, providing capabilities
 * for object creation via:
 *
 * - factories
 * - abstract factories
 * - delegator factories
 * - lazy service factories (generated proxies)
 * - initializers (interface injection)
 *
 * It also provides the ability to inject specific service instances and to
 * define aliases.
 *
 * @see ContainerInterface
 * @see DelegatorFactoryInterface
 * @see AbstractFactoryInterface
 * @see FactoryInterface
 *
 * @psalm-type AbstractFactoriesConfiguration = array<
 *      array-key,
 *      class-string<AbstractFactoryInterface>|AbstractFactoryInterface
 * >
 * @psalm-type DelegatorCallable = callable(ContainerInterface,string,callable():mixed,array<mixed>|null):mixed
 * @psalm-type DelegatorsConfiguration = array<
 *      string,
 *      array<
 *          array-key,
 *          class-string<DelegatorFactoryInterface>
 *          |class-string<object&DelegatorCallable>
 *          |DelegatorFactoryInterface
 *          |DelegatorCallable
 *      >
 * >
 * @psalm-type FactoryCallable = callable(ContainerInterface,string,array<mixed>|null):mixed
 * @psalm-type FactoriesConfiguration = array<
 *      string,
 *      class-string<FactoryInterface>|class-string<object&FactoryCallable>|FactoryInterface|FactoryCallable
 * >
 * @psalm-type InitializerCallable = callable(ContainerInterface,mixed):void
 * @psalm-type InitializersConfiguration = array<
 *      array-key,
 *      class-string<InitializerInterface>|class-string<object&InitializerCallable>|InitializerInterface|InitializerCallable