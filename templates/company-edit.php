<?php

defined('ABSPATH') || exit;

use MediaaB2B\CompanyController;

$controller = new CompanyController();

$user = $controller->getCurrentUser();

if (

    $_SERVER['REQUEST_METHOD'] === 'POST'

    && isset($_POST['mediaa_company_nonce'])

    && wp_verify_nonce(

        sanitize_text_field(

            wp_unslash(

                $_POST['mediaa_company_nonce']

            )

        ),

        'mediaa_company_update'

    )

) {

    $controller->update(

        [

            'company' => $_POST['company'] ?? '',

            'first_name' => $_POST['first_name'] ?? '',

            'last_name' => $_POST['last_name'] ?? '',

            'phone' => $_POST['phone'] ?? '',

            'email' => $_POST['email'] ?? '',

        ]

    );

    wp_safe_redirect(

        home_url('/b2b?tab=company')

    );

    exit;

}

?>

<div class="mediaa-company-card">

    <h2>Edycja danych firmy</h2>

    <form method="post">

        <?php wp_nonce_field(
            'mediaa_company_update',
            'mediaa_company_nonce'
        ); ?>

        <div class="mediaa-company-grid">

            <div class="mediaa-company-item">

                <span>Nazwa firmy</span>

                <input
                    type="text"
                    name="company"
                    value="<?php echo esc_attr(
                        get_user_meta(
                            $user->ID,
                            'billing_company',
                            true
                        )
                    ); ?>">

            </div>

            <div class="mediaa-company-item">

                <span>NIP</span>

                <p class="mediaa-company-help">

                🔒 Numer NIP nie może zostać zmieniony z poziomu panelu.

                Skontaktuj się z administratorem.

                </p>

                <input
                    type="text"
                    value="<?php echo esc_attr(
                        get_user_meta(
                            $user->ID,
                            'billing_nip',
                            true
                        )
                    ); ?>"
                    disabled>

            </div>

            <div class="mediaa-company-item">

                <span>Imię</span>

                <input
                    type="text"
                    name="first_name"
                    value="<?php echo esc_attr(
                        $user->first_name
                    ); ?>">

            </div>

            <div class="mediaa-company-item">

                <span>Nazwisko</span>

                <input
                    type="text"
                    name="last_name"
                    value="<?php echo esc_attr(
                        $user->last_name
                    ); ?>">

            </div>

            <div class="mediaa-company-item">

                <span>Telefon</span>

                <input
                    type="text"
                    name="phone"
                    value="<?php echo esc_attr(
                        get_user_meta(
                            $user->ID,
                            'billing_phone',
                            true
                        )
                    ); ?>">

            </div>

            <div class="mediaa-company-item">

                <span>Adres e-mail</span>

                <input
                    type="email"
                    name="email"
                    value="<?php echo esc_attr(
                        $user->user_email
                    ); ?>">

            </div>

        </div>

        <div class="mediaa-company-actions">

            <button
                class="mediaa-button"
                type="submit">

                Zapisz zmiany

            </button>

            <a
                class="mediaa-button"
                href="<?php echo esc_url(
                    home_url('/b2b?tab=company')
                ); ?>">

                Anuluj

            </a>

        </div>

    </form>

</div>