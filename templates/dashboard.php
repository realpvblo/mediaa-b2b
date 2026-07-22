<?php

use MediaaB2B\DashboardController;

$controller = new DashboardController();

$user = $controller->getCurrentUser();

$tab = sanitize_key(
    $_GET['tab'] ?? ''
);

?>

<div class="mediaa-b2b-dashboard">

    <h2>

        Witaj,
        <?php echo esc_html($user->display_name); ?>

    </h2>

    <p>

        Witaj w panelu Partnera B2B.

    </p>

    <div class="mediaa-dashboard-grid">

        <a href="<?php echo esc_url(
                        home_url('/b2b?tab=orders')
                    ); ?>" class="mediaa-dashboard-card <?php echo $tab === 'orders' ? 'is-active' : ''; ?>">

            <span>📦</span>

            <strong>Zamówienia</strong>

        </a>

        <a href="<?php echo esc_url(
                        home_url('/b2b?tab=company')
                    ); ?>" class="mediaa-dashboard-card <?php echo $tab === 'company' ? 'is-active' : ''; ?>">

            <span>👤</span>

            <strong>Dane firmy</strong>

        </a>

        <a href="<?php echo esc_url(
                        home_url('/b2b?tab=downloads')
                    ); ?>" class="mediaa-dashboard-card <?php echo $tab === 'downloads' ? 'is-active' : ''; ?>">

            <span>📄</span>

            <strong>Materiały</strong>

        </a>

        <a href="<?php echo esc_url(
                        home_url('/b2b?tab=partner')
                    ); ?>" class="mediaa-dashboard-card <?php echo $tab === 'partner' ? 'is-active' : ''; ?>">

            <span>🤝</span>

            <strong>Partner</strong>

        </a>

        <a href="<?php echo esc_url(
                        home_url('/b2b?tab=password')
                    ); ?>" class="mediaa-dashboard-card <?php echo $tab === 'password' ? 'is-active' : ''; ?>">

            <span>🔒</span>

            <strong>Hasło</strong>

        </a>

        <a
            href="<?php echo esc_url(
                        \wp_logout_url(
                            \home_url('/b2b')
                        )
                    ); ?>"
            class="mediaa-dashboard-card">

            <span>🚪</span>

            <strong>Wyloguj</strong>

        </a>

    </div>

    <div class="mediaa-dashboard-content">

        <?php

        if ($tab === '') {
        ?>

            <div class="mediaa-dashboard-welcome">

                <p style="margin-top: 1rem;">
                    Wybierz jedną z sekcji powyżej, aby rozpocząć pracę.
                </p>

                <div class="mediaa-dashboard-welcome-grid">

                    <div>
                        <strong>📦 Zamówienia</strong><br>
                        <small>Sprawdź historię i status swoich zamówień.</small>
                    </div>

                    <div>
                        <strong>👤 Dane firmy</strong><br>
                        <small>Zweryfikuj dane swojej firmy oraz dane kontaktowe.</small>
                    </div>

                    <div>
                        <strong>📄 Materiały</strong><br>
                        <small>Pobierz katalogi, cenniki i materiały marketingowe.</small>
                    </div>

                    <div>
                        <strong>🤝 Partner</strong><br>
                        <small>Śledź wykorzystanie swojego kodu partnerskiego, prowizje oraz historię wypłat.</small>
                    </div>

                    <div>
                        <strong>🔒 Hasło</strong><br>
                        <small>Zmień hasło do swojego konta B2B.</small>
                    </div>

                </div>

            </div>

        <?php
        } else {

            switch ($tab) {

                case 'company':
                    require MEDIAA_B2B_PATH . 'templates/company.php';
                    break;

                case 'downloads':
                    require MEDIAA_B2B_PATH . 'templates/downloads.php';
                    break;

                case 'partner':
                    require MEDIAA_B2B_PATH . 'templates/partner.php';
                    break;

                case 'password':
                    require MEDIAA_B2B_PATH . 'templates/password.php';
                    break;

                case 'orders':
                    require MEDIAA_B2B_PATH . 'templates/orders.php';
                    break;
            }
        }

        ?>

    </div>

</div>