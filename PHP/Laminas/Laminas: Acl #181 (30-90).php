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