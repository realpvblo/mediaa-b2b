

<?php

defined('ABSPATH') || exit;

$passwordController = new \MediaaB2B\PasswordController();
$passwordController->updatePassword();

?>

<div class="mediaa-company-card">

    <h2>

        Zmiana hasła

    </h2>

    <p>

        Wprowadź obecne hasło, a następnie ustaw nowe hasło do swojego konta B2B.

    </p>

    <form method="post" class="mediaa-company-form">

        <?php wp_nonce_field('mediaa_b2b_change_password', 'mediaa_b2b_nonce'); ?>

        <p>
            <label for="current_password">Obecne hasło</label>
            <input
                type="password"
                id="current_password"
                name="current_password"
                required
            >
        </p>

        <p>
            <label for="new_password">Nowe hasło</label>
            <input
                type="password"
                id="new_password"
                name="new_password"
                required
            >
        </p>

        <p>
            <label for="confirm_password">Powtórz nowe hasło</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
            >
        </p>

        <p>
            <button type="submit" name="change_password" class="button button-primary">
                Zmień hasło
            </button>
        </p>

        <p class="mediaa-forgot-password">

        <a href="<?php echo esc_url(
            wp_lostpassword_url()
        ); ?>">

        Zapomniałeś hasła?

        </a>

        </p>

    </form>

</div>