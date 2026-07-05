<?php

namespace MediaaB2B;

class PortalState
{
    public const GUEST = 'guest';

    public const PENDING = 'pending';

    public const CUSTOMER = 'customer';

    public const NOT_AUTHORIZED = 'not-authorized';

    /**
     * Get current portal state.
     */
    public static function current(): string
    {
        if (! Auth::isLoggedIn()) {
            return self::GUEST;
        }

        if (Auth::isPending()) {
            return self::PENDING;
        }

        if (Auth::isB2B()) {
            return self::CUSTOMER;
        }

        return self::NOT_AUTHORIZED;
    }
}
