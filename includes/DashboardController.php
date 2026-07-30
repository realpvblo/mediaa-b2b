<?php

namespace MediaaB2B;

class DashboardController
{
    public function getCurrentUser(): \WP_User
    {
        return \wp_get_current_user();
    }

    public function getPartnerDashboard(): array
    {
        return PartnerManager::getDashboardData(
            get_current_user_id()
        );
    }
}
