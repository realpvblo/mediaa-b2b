<?php

namespace MediaaB2B;

if (! defined('ABSPATH')) {
    exit;
}

class CommissionAdminController
{
    public function register(): void
    {
        add_action(
            'admin_menu',
            [$this, 'registerMenu']
        );
    }

    public function registerMenu(): void
    {
        add_submenu_page(
            'mediaa-b2b',
            __('Prowizje', 'mediaa-b2b'),
            __('Prowizje', 'mediaa-b2b'),
            'manage_woocommerce',
            'mediaa-b2b-commissions',
            [$this, 'renderCommissionPage']
        );
    }

    public function renderCommissionPage(): void
    {
        $commissions = CommissionManager::getCommissionList();

        ?>

        <div class="wrap">

            <h1>Prowizje</h1>

            <p>
                Historia wszystkich prowizji wygenerowanych przez partnerów.
            </p>

            <?php if (empty($commissions)) : ?>

                <div class="notice notice-info">

                    <p>
                        Brak prowizji.
                    </p>

                </div>

            <?php else : ?>

                <table class="widefat striped">

                    <thead>

                        <tr>

                            <th>Partner</th>

                            <th>Kod</th>

                            <th>Zamówienie</th>

                            <th>Wartość zamówienia</th>

                            <th>Prowizja</th>

                            <th>Status</th>

                            <th>Data</th>

                            <th>Akcja</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($commissions as $commission) : ?>

                            <tr>

                                <td>

                                    <?php echo esc_html(
                                        $commission['partner']
                                    ); ?>

                                </td>

                                <td>

                                    <span class="mediaa-code">

                                        <?php echo esc_html(
                                            $commission['code']
                                        ); ?>

                                    </span>

                                </td>

                                <td>

                                    <a
                                        href="<?php echo esc_url(
                                            admin_url(
                                                'post.php?post=' .
                                                $commission['order_id'] .
                                                '&action=edit'
                                            )
                                        ); ?>">

                                        #<?php echo esc_html(
                                            $commission['order_id']
                                        ); ?>

                                    </a>

                                </td>

                                <td>

                                    <?php echo wp_kses_post(
                                        $commission['order_total']
                                    ); ?>

                                </td>

                                <td>

                                    <strong>

                                        <?php echo wp_kses_post(
                                            $commission['commission']
                                        ); ?>

                                    </strong>

                                    <br>

                                    <small>

                                        <?php echo esc_html(
                                            $commission['rate']
                                        ); ?>%

                                    </small>

                                </td>

                                <td>

                                    <?php if (
                                        $commission['status'] === 'pending'
                                    ) : ?>

                                        <span
                                            style="
                                                color:#996800;
                                                font-weight:600;
                                            ">

                                            🟡 Do wypłaty

                                        </span>

                                    <?php else : ?>

                                        <span
                                            style="
                                                color:#008a20;
                                                font-weight:600;
                                            ">

                                            🟢 Wypłacona

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php echo esc_html(
                                        date_i18n(
                                            'd.m.Y',
                                            strtotime(
                                                $commission['created_at']
                                            )
                                        )
                                    ); ?>

                                </td>

                                <td>

                                    <a
                                        href="#"
                                        class="button button-primary">

                                        👁 Zobacz

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

        </div>

        <?php
    }
}