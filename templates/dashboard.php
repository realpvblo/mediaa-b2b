<?php

use MediaaB2B\DashboardController;

$controller = new DashboardController();

$user = $controller->getCurrentUser();

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

        <a href="#" class="mediaa-dashboard-card">

            <span>📦</span>

            <strong>Zamówienia</strong>

        </a>

        <a href="#" class="mediaa-dashboard-card">

            <span>👤</span>

            <strong>Dane firmy</strong>

        </a>

        <a href="#" class="mediaa-dashboard-card">

            <span>📄</span>

            <strong>Materiały</strong>

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

</div>