     * Note that the newly created permission is not added to the RBAC system yet.
     * You must fill in the needed data and call [[add()]] to add it to the system.
     * @param string $name the permission name
     * @return Permission the new Permission object
     */
    public function createPermission($name);

    /**
     * Adds a role, permission or rule to the RBAC system.
     * @param Role|Permission|Rule $object
     * @return bool whether the role, permission or rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    public function add($object);

    /**
     * Removes a role, permission or rule from the RBAC system.
     * @param Role|Permission|Rule $object
     * @return bool whether the role, permission or rule is successfully removed
     */
    public function remove($object);

    /**
     * Updates the specified role, permission or rule in the system.
     * @param string $name the old name of the role, permission or rule
     * @param Role|Permission|Rule $object
     * @return bool whether the update is successful
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    public function update($name, $object);

    /**
     * Returns the named role.
     * @param string $name the role name.
     * @return Role|null the role corresponding to the specified name. Null is returned if no such role.
     */
    public function getRole($name);

    /**
     * Returns all roles in the system.
     * @return Role[] all roles in the system. The array is indexed by the role names.
     */
    public function getRoles();

    /**
     * Returns the roles that are assigned to the user via [[assign()]].
     * Note that child roles that are not assigned directly to the user will not be returned.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Role[] all roles directly assigned to the user. The array is indexed by the role names.
     */
    public function getRolesByUser($userId);

    /**
     * Returns child roles of the role specified. Depth isn't limited.
     * @param string $roleName name of the role to file child roles for
     * @return Role[] Child roles. The array is indexed by the role names.
     * First element is an instance of the parent Role itself.
     * @throws \yii\base\InvalidParamException if Role was not found that are getting by $roleName
     * @since 2.0.10
     */
    public function getChildRoles($roleName);

    /**
     * Returns the named permission.
     * @param string $name the permission name.
     * @return Permission|null the permission corresponding to the specified name. Null is returned if no such permission.
     */
    public function getPermission($name);

    /**
     * Returns all permissions in the system.
     * @return Permission[] all permissions in the system. The array is indexed by the permission names.
     */
    public function getPermissions();

    /**
     * Returns all permissions that the specified role represents.
     * @param string $roleName the role name
     * @return Permission[] all permissions that the role represents. The array is indexed by the permission names.
     */
    public function getPermissionsByRole($roleName);