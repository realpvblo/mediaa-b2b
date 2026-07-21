<?php

namespace MediaaB2B;

defined('ABSPATH') || exit;

class PasswordController
{

    public function updatePassword()
    {

        if (! isset($_POST['change_password'])) {

            return;

        }

        if (
            ! isset($_POST['mediaa_b2b_nonce']) ||
            ! wp_verify_nonce($_POST['mediaa_b2b_nonce'], 'mediaa_b2b_change_password')
        ) {

            echo '<div class="notice notice-error"><p>Nieprawidłowy token bezpieczeństwa.</p></div>';

            return;

        }

        $user = wp_get_current_user();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (! wp_check_password($currentPassword, $user->user_pass, $user->ID)) {

            echo '<div class="notice notice-error"><p>Obecne hasło jest nieprawidłowe.</p></div>';

            return;

        }

        if ($newPassword !== $confirmPassword) {

            echo '<div class="notice notice-error"><p>Nowe hasła nie są identyczne.</p></div>';

            return;

        }

        if (strlen($newPassword) < 8) {

            echo '<div class="notice notice-error"><p>Hasło musi mieć co najmniej 8 znaków.</p></div>';

            return;

        }

        wp_set_password($newPassword, $user->ID);

        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);

        echo '<div class="notice notice-success"><p>Hasło zostało pomyślnie zmienione.</p></div>';

    }

}