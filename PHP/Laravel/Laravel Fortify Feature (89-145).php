    public static function resetPasswords()
    {
        return 'reset-passwords';
    }

    /**
     * Enable the email verification feature.
     *
     * @return string
     */
    public static function emailVerification()
    {
        return 'email-verification';
    }

    /**
     * Enable the update profile information feature.
     *
     * @return string
     */
    public static function updateProfileInformation()
    {
        return 'update-profile-information';
    }

    /**
     * Enable the update password feature.
     *
     * @return string
     */
    public static function updatePasswords()
    {
        return 'update-passwords';
    }

    /**
     * Enable the two factor authentication feature.
     *
     * @param  array  $options
     * @return string
     */
    public static function twoFactorAuthentication(array $options = [])
    {
        if (! empty($options)) {
            config(['fortify-options.two-factor-authentication' => $options]);
        }

        return 'two-factor-authentication';
    }
}
