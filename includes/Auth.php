<?php

namespace MediaaB2B;

class Auth
{
    /**
     * Is user logged in?
     */
    public static function isLoggedIn(): bool
    {
        return \is_user_logged_in();
    }

    /**
     * Current WP_User.
     */
    public static function user(): \WP_User
    {
        return \wp_get_current_user();
    }

    /**
     * Current user ID.
     */
    public static function id(): int
    {
        return self::user()->ID;
    }

    /**
     * User roles.
     */
    public static function roles(): array
    {
        return self::user()->roles;
    }

    /**
     * Has specific role?
     */
    public static function hasRole(string $role): bool
    {
        return in_array($role, self::roles(), true);
    }

    /**
     * Administrator?
     */
    public static function isAdmin(): bool
    {
        return self::hasRole('administrator');
    }

    /**
     * B2B Customer?
     */
    public static function isB2B(): bool
    {
        return self::hasRole(Roles::ROLE_B2B_CUSTOMER);
    }

    /**
     * Pending?
     */
    public static function isPending(): bool
    {
        return self::hasRole(Roles::ROLE_B2B_PENDING);
    }
}
