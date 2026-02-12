<?php

declare(strict_types=1);

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
                ],
                'byPrivilegeId' => [],
            ],
            'byRoleId' => [],
        ],
        'byResourceId' => [],
    ];

    /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Role\RoleInterface|string       $role
     * @param  Role\RoleInterface|string|array $parents
     * @throws InvalidArgumentException
     * @return Acl Provides a fluent interface
     */
    public function addRole($role, $parents = null)
    {
        if (is_string($role)) {
            $role = new Role\GenericRole($role);
        } elseif (! $role instanceof Role\RoleInterface) {
            throw new InvalidArgumentException(
                'addRole() expects $role to be of type Laminas\Permissions\Acl\Role\RoleInterface'
            );
        }