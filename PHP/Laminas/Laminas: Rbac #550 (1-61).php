<?php

declare(strict_types=1);

namespace Laminas\Permissions\Rbac;

use function array_values;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function sprintf;

/**
 * @final
 */
class Rbac
{
    /** @var array<string, RoleInterface> */
    protected $roles = [];

    /**
     * flag: whether or not to create roles automatically if
     * they do not exist.
     *
     * @var bool
     */
    protected $createMissingRoles = false;

    public function setCreateMissingRoles(bool $createMissingRoles): void
    {
        $this->createMissingRoles = $createMissingRoles;
    }

    public function getCreateMissingRoles(): bool
    {
        return $this->createMissingRoles;
    }

    /**
     * Add a child.
     *
     * @param  string|RoleInterface $role
     * @param  null|array|RoleInterface $parents
     * @throws Exception\InvalidArgumentException If $role is not a string or RoleInterface.
     */
    public function addRole($role, $parents = null): void
    {
        if (is_string($role)) {
            $role = new Role($role);
        }
        if (! $role instanceof RoleInterface) {
            throw new Exception\InvalidArgumentException(
                'Role must be a string or implement Laminas\Permissions\Rbac\RoleInterface'
            );
        }

        if ($parents !== null) {
            $parents = is_array($parents) ? $parents : [$parents];
            /** @var RoleInterface|string $parent */
            foreach ($parents as $parent) {