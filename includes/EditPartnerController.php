<?php

namespace MediaaB2B;

if (! defined('ABSPATH')) {
    exit;
}

class EditPartnerController
{
    public function register(): void
    {
        add_action(
            'admin_menu',
            [$this, 'registerPage']
        );
        add_action(
            'admin_post_mediaa_b2b_save_partner',
            [$this, 'savePartner']
        );
    }

    public function registerPage(): void
    {
        add_submenu_page(
            null,
            'Edytuj partnera',
            'Edytuj partnera',
            'manage_options',
            'mediaa-b2b-edit-partner',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        $userId = absint($_GET['user_id'] ?? 0);

        $user = get_user_by('id', $userId);

        if (! $user instanceof \WP_User) {
            wp_die('Nie znaleziono użytkownika.');
        }

        $company = (string) get_user_meta(
            $userId,
            'billing_company',
            true
        );

        $partnerCode = PartnerManager::getCode($userId);

        $partnerRate = PartnerManager::getRate($userId);

        ?>

        <div class="wrap">

        <?php
        $status = sanitize_text_field(
            wp_unslash($_GET['status'] ?? '')
        );

        $messages = [
            'saved' => [
                'success',
                'Partner został zapisany.',
            ],
            'duplicate-code' => [
                'error',
                'Ten kod partnera jest już używany.',
            ],
            'empty-code' => [
                'error',
                'Kod partnera nie może być pusty.',
            ],
            'invalid-rate' => [
                'error',
                'Prowizja musi mieć wartość od 0 do 100%.',
            ],
        ];

        if (isset($messages[$status])) {
            [$type, $message] = $messages[$status];

            printf(
                '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
                esc_attr($type),
                esc_html($message)
            );
        }

        ?>

            <h1>Edytuj partnera</h1>

            <form
                method="post"
                action="<?php echo esc_url(admin_url('admin-post.php')); ?>">

                <input
                    type="hidden"
                    name="action"
                    value="mediaa_b2b_save_partner">

                <input
                    type="hidden"
                    name="user_id"
                    value="<?php echo esc_attr($userId); ?>">

                <?php
                wp_nonce_field(
                    'mediaa_b2b_save_partner',
                    'mediaa_b2b_nonce'
                );
                ?>

                <table class="form-table">

                    <tr>
                        <th>Firma</th>
                        <td><?php echo esc_html($company); ?></td>
                    </tr>

                    <tr>
                        <th>E-mail</th>
                        <td><?php echo esc_html($user->user_email); ?></td>
                    </tr>

                    <tr>
                        <th><label for="partner_code">Kod partnera</label></th>
                        <td>
                            <input
                                id="partner_code"
                                name="partner_code"
                                type="text"
                                class="regular-text"
                                value="<?php echo esc_attr($partnerCode); ?>">
                        </td>
                    </tr>

                    <tr>
                        <th><label for="partner_rate">Prowizja (%)</label></th>
                        <td>
                            <input
                                id="partner_rate"
                                name="partner_rate"
                                type="number"
                                min="0"
                                max="100"
                                step="0.01"
                                value="<?php echo esc_attr($partnerRate); ?>">
                        </td>
                    </tr>

                </table>

                <?php submit_button('Zapisz partnera'); ?>

            </form>

        </div>

        <?php
    }

    public function savePartner(): void
    {
        if (! current_user_can('manage_options')) {
            wp_die('Brak uprawnień.');
        }

        if (
            ! isset($_POST['mediaa_b2b_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash($_POST['mediaa_b2b_nonce'])
                ),
                'mediaa_b2b_save_partner'
            )
        ) {
            wp_safe_redirect(
                admin_url('admin.php?page=mediaa-b2b&status=error')
            );

            exit;
        }

        $userId = absint($_POST['user_id'] ?? 0);

        $partnerCode = sanitize_text_field(
            wp_unslash($_POST['partner_code'] ?? '')
        );

        $partnerRate = wp_unslash(
            $_POST['partner_rate'] ?? '0'
        );

        $partnerRate = str_replace(
            ',',
            '.',
            $partnerRate
        );

        $partnerRate = (float) $partnerRate;

        $partnerCode = strtoupper(trim($partnerCode));

        if ($partnerCode === '') {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=mediaa-b2b-edit-partner&user_id='
                    . $userId
                    . '&status=empty-code'
                )
            );

            exit;
        }

        if ($partnerRate < 0 || $partnerRate > 100) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=mediaa-b2b-edit-partner&user_id='
                    . $userId
                    . '&status=invalid-rate'
                )
            );

            exit;
        }

        if (PartnerManager::codeExists($partnerCode, $userId)) {
            wp_safe_redirect(
                admin_url(
                    'admin.php?page=mediaa-b2b-edit-partner&user_id='
                    . $userId
                    . '&status=duplicate-code'
                )
            );

            exit;
        }

        PartnerManager::updatePartner(
            $userId,
            $partnerCode,
            $partnerRate
        );

        wp_safe_redirect(
            admin_url(
                'admin.php?page=mediaa-b2b-edit-partner&user_id=' . $userId . '&status=saved'
            )
        );

        exit;
    }
}