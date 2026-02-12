     * Creates a new Role object.
     * Note that the newly created role is not added to the RBAC system yet.
     * You must fill in the needed data and call [[add()]] to add it to the system.
     * @param string $name the role name
     * @return Role the new Role object
     */
    public function createRole($name);

    /**
     * Creates a new Permission object.
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
