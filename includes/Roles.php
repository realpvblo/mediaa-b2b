<?php

namespace MediaaB2B;

class Roles
{
    public const PENDING = 'b2b_pending';
    public const CUSTOMER = 'b2b_customer';

    /**
     * Create all B2B roles.
     */
    public static function create(): void
    {
        \add_role(
            self::PENDING,
            'B2B Pending',
            [
                'read' => true,
            ]
        );

        \add_role(
            self::CUSTOMER,
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
        \remove_role(self::PENDING);
        \remove_role(self::CUSTOMER);
    }

    /**
     * Check if user is B2B.
     */
    public static function isB2B(\WP_User $user): bool
    {
        return in_array(self::CUSTOMER, $user->roles, true);
    }

    /**
     * Check if user is waiting for approval.
     */
    public static function isPending(\WP_User $user): bool
    {
        return in_array(self::PENDING, $user->roles, true);
    }
}
