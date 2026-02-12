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
 * Query Language and Repository API. Instantiation is done through
 * the static create() method. The quickest way to obtain a fully
 * configured EntityManager is:
 *
 *     use Doctrine\ORM\Tools\ORMSetup;
 *     use Doctrine\ORM\EntityManager;
 *
 *     $paths = ['/path/to/entity/mapping/files'];
 *
 *     $config = ORMSetup::createAttributeMetadataConfiguration($paths);
 *     $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config);
 *     $entityManager = new EntityManager($connection, $config);
 *
 * For more information see
 * {@link http://docs.doctrine-project.org/projects/doctrine-orm/en/stable/reference/configuration.html}
 *
 * You should never attempt to inherit from the EntityManager: Inheritance
 * is not a valid extension point for the EntityManager. Instead you
 * should take a look at the {@see \Doctrine\ORM\Decorator\EntityManagerDecorator}
 * and wrap your entity manager in a decorator.