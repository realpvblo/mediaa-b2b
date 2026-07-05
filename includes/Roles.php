<?php

namespace MediaaB2B;

class Roles
{
    public const ROLE_B2B_PENDING = 'b2b_pending';
    public const ROLE_B2B_CUSTOMER = 'b2b_customer';

    /**
     * Create all B2B roles.
     */
    public static function create(): void
    {
        \add_role(
            self::ROLE_B2B_PENDING,
            'B2B Pending',
            [
                'read' => true,
            ]
        );

        \add_role(
            self::ROLE_B2B_CUSTOMER,
            'B2B Customer',
            [
                'read' => true,
            ]
        );
    }

    /**
     * Remove all B2B roles.
     */
    public static function remove(): void
    {
        \remove_role(self::ROLE_B2B_PENDING);
        \remove_role(self::ROLE_B2B_CUSTOMER);
    }

    /**
     * Check if user is a B2B customer.
     */
    public static function isCustomer(\WP_User $user): bool
    {
        return in_array(self::ROLE_B2B_CUSTOMER, $user->roles, true);
    }

    /**
     * Check if user is waiting for approval.
     */
    public static function isPending(\WP_User $user): bool
    {
        return in_array(self::ROLE_B2B_PENDING, $user->roles, true);
    }

    /**
     * Check if user is Admin.
     */

    public static function isAdministrator(\WP_User $user): bool
    {
        return \in_array(
            'administrator',
            $user->roles,
            true
        );
    }

    public static function canAccessPortal(\WP_User $user): bool
    {
        return
            self::isAdministrator($user)
            || self::isCustomer($user);
    }
}
