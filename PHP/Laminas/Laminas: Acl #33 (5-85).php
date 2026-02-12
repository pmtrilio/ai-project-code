namespace Laminas\Permissions\Acl;

use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Exception\ExceptionInterface;
use Laminas\Permissions\Acl\Exception\InvalidArgumentException;
use Laminas\Permissions\Acl\Exception\RuntimeException;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Throwable;

use function array_key_exists;
use function array_keys;
use function array_pop;
use function is_array;
use function is_string;
use function sprintf;
use function strtoupper;

class Acl implements AclInterface
{
    /**
     * Rule type: allow
     */
    public const TYPE_ALLOW = 'TYPE_ALLOW';

    /**
     * Rule type: deny
     */
    public const TYPE_DENY = 'TYPE_DENY';

    /**
     * Rule operation: add
     */
    public const OP_ADD = 'OP_ADD';

    /**
     * Rule operation: remove
     */
    public const OP_REMOVE = 'OP_REMOVE';

    /**
     * Role registry
     *
     * @var Role\Registry|null
     */
    protected $roleRegistry;

    /**
     * Resource tree
     *
     * @var array
     */
    protected $resources = [];

    /**
     * Resources by resourceId plus a null element
     * Used to speed up setRule()
     *
     * @var array<int|string, ResourceInterface|null>
     */
    private $resourcesById = [null];

    /** @var Role\RoleInterface|null */
    protected $isAllowedRole;

    /** @var ResourceInterface|null */
    protected $isAllowedResource;

    /** @var string|null */
    protected $isAllowedPrivilege;

    /**
     * ACL rules; whitelist (deny everything to all) by default
     *
     * @var array
     */
    protected $rules = [
        'allResources' => [
            'allRoles' => [
                'allPrivileges' => [
                    'type'   => self::TYPE_DENY,
                    'assert' => null,