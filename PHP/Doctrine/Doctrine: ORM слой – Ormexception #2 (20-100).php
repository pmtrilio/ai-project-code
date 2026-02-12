use Doctrine\ORM\Exception\NamedQueryNotFound;
use Doctrine\ORM\Exception\ProxyClassesAlwaysRegenerating;
use Doctrine\ORM\Exception\UnexpectedAssociationValue;
use Doctrine\ORM\Exception\UnknownEntityNamespace;
use Doctrine\ORM\Exception\UnrecognizedIdentifierFields;
use Doctrine\ORM\Persisters\Exception\CantUseInOperatorOnCompositeKeys;
use Doctrine\ORM\Persisters\Exception\InvalidOrientation;
use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use Doctrine\ORM\Repository\Exception\InvalidFindByCall;
use Doctrine\ORM\Repository\Exception\InvalidMagicMethodCall;
use Doctrine\ORM\Tools\Exception\NotSupported;
use Exception;

use function sprintf;

/**
 * Base exception class for all ORM exceptions.
 *
 * @deprecated Use Doctrine\ORM\Exception\ORMException for catch and instanceof
 */
class ORMException extends Exception
{
    /**
     * @deprecated Use Doctrine\ORM\Exception\MissingMappingDriverImplementation
     *
     * @return ORMException
     */
    public static function missingMappingDriverImpl()
    {
        return MissingMappingDriverImplementation::create();
    }

    /**
     * @deprecated Use Doctrine\ORM\Exception\NamedQueryNotFound
     *
     * @param string $queryName
     *
     * @return ORMException
     */
    public static function namedQueryNotFound($queryName)
    {
        return NamedQueryNotFound::fromName($queryName);
    }

    /**
     * @deprecated Use Doctrine\ORM\Exception\NamedNativeQueryNotFound
     *
     * @param string $nativeQueryName
     *
     * @return ORMException
     */
    public static function namedNativeQueryNotFound($nativeQueryName)
    {
        return NamedNativeQueryNotFound::fromName($nativeQueryName);
    }

    /**
     * @deprecated Use Doctrine\ORM\Persisters\Exception\UnrecognizedField
     *
     * @param string $field
     *
     * @return ORMException
     */
    public static function unrecognizedField($field)
    {
        return new UnrecognizedField(sprintf('Unrecognized field: %s', $field));
    }

    /**
     * @deprecated Use Doctrine\ORM\Exception\UnexpectedAssociationValue
     *
     * @param string $class
     * @param string $association
     * @param string $given
     * @param string $expected
     *
     * @return ORMException
     */
    public static function unexpectedAssociationValue($class, $association, $given, $expected)
    {
        return UnexpectedAssociationValue::create($class, $association, $given, $expected);