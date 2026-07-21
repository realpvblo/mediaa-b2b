<?php

namespace MediaaB2B;

class CompanyController
{
    /**
     * Get current user.
     */
    public function getCurrentUser(): \WP_User
    {
        return \wp_get_current_user();
    }

    /**
     * Update company data.
     */
    public function update(array $data): bool
    {
        $user = $this->getCurrentUser();

        \wp_update_user(
            [
                'ID' => $user->ID,
                'first_name' => sanitize_text_field(
                    $data['first_name']
                ),
                'last_name' => sanitize_text_field(
                    $data['last_name']
                ),
                'user_email' => sanitize_email(
                    $data['email']
                ),
            ]
        );

        update_user_meta(
            $user->ID,
            'billing_company',
            sanitize_text_field(
                $data['company']
            )
        );

        update_user_meta(
            $user->ID,
            'billing_phone',
            sanitize_text_field(
                $data['phone']
            )
        );

        return true;
    }
}
