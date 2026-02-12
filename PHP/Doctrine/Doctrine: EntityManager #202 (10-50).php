use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\PersistentObject;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\LockMode;
use Doctrine\Deprecations\Deprecation;
use Doctrine\ORM\Exception\EntityManagerClosed;
use Doctrine\ORM\Exception\InvalidHydrationMode;
use Doctrine\ORM\Exception\MismatchedEventManager;
use Doctrine\ORM\Exception\MissingIdentifierField;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Exception\UnrecognizedIdentifierFields;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Proxy\DefaultProxyClassNameResolver;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\Mapping\MappingException;
use InvalidArgumentException;

use function array_keys;
use function class_exists;
use function get_debug_type;
use function gettype;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function ltrim;
use function sprintf;
use function strpos;

/**
 * The EntityManager is the central access point to ORM functionality.
 *
 * It is a facade to all different ORM subsystems such as UnitOfWork,