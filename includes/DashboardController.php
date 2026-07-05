<?php

namespace MediaaB2B;

class DashboardController
{
    public function getCurrentUser(): \WP_User
    {
        return \wp_get_current_user();
    }
}
