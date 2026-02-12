     * Specify the team model that should be used by Jetstream.
     *
     * @param  string  $model
     * @return static
     */
    public static function useTeamModel(string $model)
    {
        static::$teamModel = $model;

        return new static;
    }

    /**
     * Get the name of the membership model used by the application.
     *
     * @return string
     */
    public static function membershipModel()
    {
        return static::$membershipModel;
    }

    /**
     * Specify the membership model that should be used by Jetstream.
     *
     * @param  string  $model
     * @return static
     */
    public static function useMembershipModel(string $model)
    {
        static::$membershipModel = $model;

        return new static;
    }

    /**
     * Get the name of the team invitation model used by the application.
     *
     * @return string
     */
    public static function teamInvitationModel()
    {
        return static::$teamInvitationModel;
    }

    /**
     * Specify the team invitation model that should be used by Jetstream.
     *
     * @param  string  $model
     * @return static
     */
    public static function useTeamInvitationModel(string $model)
    {
        static::$teamInvitationModel = $model;

        return new static;
    }

    /**
     * Register a class / callback that should be used to create teams.
     *
     * @param  string  $class
     * @return void
     */
    public static function createTeamsUsing(string $class)
    {
        return app()->singleton(CreatesTeams::class, $class);