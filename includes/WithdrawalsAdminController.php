<?php

namespace MediaaB2B;

if (! defined('ABSPATH')) {
    exit;
}

class WithdrawalsAdminController
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
            'Wypłaty',
            'Wypłaty',
            'manage_woocommerce',
            'mediaa-b2b-withdrawals',
            [$this, 'renderPage']
        );
    }

    public function renderPage(): void
    {
        if (
            isset($_POST['mediaa_process_withdrawal'])
            && current_user_can('manage_woocommerce')
        ) {

            $withdrawalId = (int) $_POST['withdrawal_id'];

            check_admin_referer(
                'mediaa_process_withdrawal_' . $withdrawalId
            );

            WithdrawalManager::markAsPaid(
                $withdrawalId
            );

            // wp_safe_redirect(
            //     admin_url(
            //         'admin.php?page=mediaa-b2b-withdrawals'
            //     )
            // );

            wp_safe_redirect(
                add_query_arg(
                    [
                        'page' => 'mediaa-b2b-withdrawals',
                        'processed' => 1,
                    ],
                    admin_url('admin.php')
                )
            );

            exit;
        }

        $withdrawals = WithdrawalManager::getList();

        ?>

        <div class="wrap">

            <h1>

                Wypłaty

            </h1>

            <?php if (
                isset($_GET['processed'])
                && $_GET['processed'] === '1'
            ) : ?>

                <div class="notice notice-success is-dismissible">

                    <p>

                        ✓ Wypłata została oznaczona jako zrealizowana.

                    </p>

                </div>

            <?php endif; ?>

            <p>

                Historia wszystkich wypłat prowizji.

            </p>

            <?php if (empty($withdrawals)) : ?>

                <div class="notice notice-info">

                    <p>

                        Brak wypłat.

                    </p>

                </div>

            <?php else : ?>

                <table class="widefat striped">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Partner</th>

                            <th>Kwota</th>

                            <th>Status</th>

                            <th>Zgłoszono</th>

                            <th>Zrealizowano</th>

                            <th>Administrator</th>

                            <th>Akcja</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($withdrawals as $withdrawal) : ?>

                            <tr>

                                <td>

                                    #<?php echo esc_html(
                                        $withdrawal['id']
                                    ); ?>

                                </td>

                                <td>

                                    <?php echo esc_html(
                                        $withdrawal['partner']
                                    ); ?>

                                </td>

                                <td>

                                    <strong>

                                        <?php echo wp_kses_post(
                                            $withdrawal['amount']
                                        ); ?>

                                    </strong>

                                </td>

                                <td>

                                    <?php

                                    switch ($withdrawal['status']) {

                                        case WithdrawalManager::STATUS_PENDING:

                                            ?>

                                            <span style="color:#996800;font-weight:600;">

                                                🟡 Oczekuje

                                            </span>

                                            <?php

                                            break;

                                        case WithdrawalManager::STATUS_PAID:

                                            ?>

                                            <span style="color:#008a20;font-weight:600;">

                                                🟢 Zrealizowana

                                            </span>

                                            <?php

                                            break;

                                        default:

                                            ?>

                                            <span style="color:#cc0000;font-weight:600;">

                                                🔴 Anulowana

                                            </span>

                                            <?php
                                    }

                                    ?>

                                </td>

                                <td>

                                    <?php

                                    echo esc_html(
                                        date_i18n(
                                            'd.m.Y H:i',
                                            strtotime(
                                                $withdrawal['requested_at']
                                            )
                                        )
                                    );

                                    ?>

                                </td>

                                <td>

                                    <?php

                                    if ($withdrawal['processed_at']) {

                                        echo esc_html(
                                            date_i18n(
                                                'd.m.Y H:i',
                                                strtotime(
                                                    $withdrawal['processed_at']
                                                )
                                            )
                                        );

                                    } else {

                                        echo '&mdash;';

                                    }

                                    ?>

                                </td>

                                <td>

                                    <?php

                                    if ($withdrawal['processed_by']) {

                                        $admin = get_userdata(
                                            $withdrawal['processed_by']
                                        );

                                        echo esc_html(
                                            $admin?->display_name ?? '-'
                                        );

                                    } else {

                                        echo '&mdash;';

                                    }

                                    ?>

                                </td>

                                <td>

                                    <?php if (
                                        $withdrawal['status'] === WithdrawalManager::STATUS_PENDING
                                    ) : ?>

                                    <form method="post">

                                        <?php
                                        wp_nonce_field(
                                            'mediaa_process_withdrawal_' .
                                            $withdrawal['id']
                                        );
                                        ?>

                                        <input
                                            type="hidden"
                                            name="withdrawal_id"
                                            value="<?php echo esc_attr(
                                                $withdrawal['id']
                                            ); ?>">

                                        <button
                                            class="button button-primary"
                                            name="mediaa_process_withdrawal"
                                            value="1">

                                            ✓ Zrealizuj

                                        </button>

                                    </form>

                                    <?php else : ?>

                                    —

                                    <?php endif; ?>

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