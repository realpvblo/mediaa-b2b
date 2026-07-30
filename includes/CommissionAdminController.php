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
            'Prowizje',
            'Prowizje',
            'manage_woocommerce',
            'mediaa-b2b-commissions',
            [$this, 'renderCommissionPage']
        );
    }

    public function renderCommissionPage(): void
    {
        if (
            isset($_GET['commission'])
            && is_numeric($_GET['commission'])
        ) {
            $this->renderCommissionDetails(
                (int) $_GET['commission']
            );

            return;
        }

        $this->renderCommissionList();
    }

    private function renderCommissionList(): void
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

                            <th>ID</th>

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

                                    #<?php echo esc_html(
                                        $commission['id']
                                    ); ?>

                                </td>

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
                                        href="<?php echo esc_url(
                                            admin_url(
                                                'admin.php?page=mediaa-b2b-commissions&commission=' .
                                                $commission['id']
                                            )
                                        ); ?>"
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

    private function renderCommissionDetails(
    int $commissionId
    ): void
    {
        if (
            isset($_POST['mediaa_mark_paid'])
            && current_user_can('manage_woocommerce')
        ) {

            check_admin_referer(
                'mediaa_mark_paid_' . $commissionId
            );

            WithdrawalManager::createFromCommission(
                $commissionId
            );

            // wp_safe_redirect(
            //     admin_url(
            //         'admin.php?page=mediaa-b2b-commissions&commission=' .
            //         $commissionId .
            //         '&created=1'
            //     )
            // );

            wp_safe_redirect(
                add_query_arg(
                    [
                        'page' => 'mediaa-b2b-commissions',
                        'commission' => $commissionId,
                        'created' => 1,
                    ],
                    admin_url('admin.php')
                )
            );

            exit;
        }

        $commission = CommissionManager::getCommissionDetails(
            $commissionId
        );

        if (! $commission) {

            ?>

            <div class="wrap">

                <h1>Prowizja nie istnieje</h1>

                <a
                    href="<?php echo esc_url(
                        admin_url(
                            'admin.php?page=mediaa-b2b-commissions'
                        )
                    ); ?>"
                    class="button">

                    ← Powrót

                </a>

            </div>

            <?php

            return;
        }

        /** @var WC_Order|null $order */
        $order = $commission['order'];

        ?>

        <div class="wrap">

            <p>

                <a
                    href="<?php echo esc_url(
                        admin_url(
                            'admin.php?page=mediaa-b2b-commissions'
                        )
                    ); ?>"
                    class="button">

                    ← Powrót do listy

                </a>

            </p>

            <h1>

                Prowizja #<?php echo esc_html(
                    $commission['id']
                ); ?>

            </h1>

            <?php if (
                isset($_GET['created'])
                && $_GET['created'] === '1'
            ) : ?>

                <div class="notice notice-success is-dismissible">

                    <p>

                        ✓ Utworzono wypłatę. Możesz ją zrealizować w zakładce <strong>Wypłaty</strong>.

                    </p>

                </div>

            <?php endif; ?>

            <table class="form-table">

                <tbody>

                    <tr>

                        <th>Partner</th>

                        <td>

                            <strong>

                                <?php echo esc_html(
                                    $commission['partner']
                                ); ?>

                            </strong>

                        </td>

                    </tr>

                    <tr>

                        <th>Kod partnerski</th>

                        <td>

                            <?php echo esc_html(
                                $commission['code']
                            ); ?>

                        </td>

                    </tr>

                    <tr>

                        <th>Prowizja</th>

                        <td>

                            <strong>

                                <?php echo wp_kses_post(
                                    $commission['commission']
                                ); ?>

                            </strong>

                            (<?php echo esc_html(
                                $commission['rate']
                            ); ?>%)

                        </td>

                    </tr>

                    <tr>

                        <th>Wartość zamówienia</th>

                        <td>

                            <?php echo wp_kses_post(
                                $commission['order_total']
                            ); ?>

                        </td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>

                            <?php
                            if ($commission['status'] === 'pending') {
                                echo '🟡 Do wypłaty';
                            } else {
                                echo '🟢 Wypłacona';
                            }
                            ?>

                        </td>

                    </tr>

                    <tr>

                        <th>Data naliczenia</th>

                        <td>

                            <?php

                            echo esc_html(
                                date_i18n(
                                    'd.m.Y H:i',
                                    strtotime(
                                        $commission['created_at']
                                    )
                                )
                            );

                            ?>

                        </td>

                    </tr>

                </tbody>

            </table>

            <?php if ($order) : ?>

                <hr>

                <h2>

                    Zamówienie #<?php echo esc_html(
                        $order->get_id()
                    ); ?>

                </h2>

                <table class="form-table">

                    <tbody>

                        <tr>

                            <th>Klient</th>

                            <td>

                                <?php echo esc_html(
                                    $order->get_formatted_billing_full_name()
                                ); ?>

                            </td>

                        </tr>

                        <tr>

                            <th>Email</th>

                            <td>

                                <?php echo esc_html(
                                    $order->get_billing_email()
                                ); ?>

                            </td>

                        </tr>

                        <tr>

                            <th>Telefon</th>

                            <td>

                                <?php echo esc_html(
                                    $order->get_billing_phone()
                                ); ?>

                            </td>

                        </tr>

                        <tr>

                            <th>Łączna kwota</th>

                            <td>

                                <?php echo wp_kses_post(
                                    $order->get_formatted_order_total()
                                ); ?>

                            </td>

                        </tr>

                    </tbody>

                </table>

                <h2>

                    Produkty

                </h2>

                <table class="widefat striped">

                    <thead>

                        <tr>

                            <th>Produkt</th>

                            <th>Ilość</th>

                            <th>Suma</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($order->get_items() as $item) : ?>

                            <tr>

                                <td>

                                    <?php echo esc_html(
                                        $item->get_name()
                                    ); ?>

                                </td>

                                <td>

                                    <?php echo esc_html(
                                        $item->get_quantity()
                                    ); ?>

                                </td>

                                <td>

                                    <?php

                                    echo wp_kses_post(
                                        wc_price(
                                            $item->get_total()
                                        )
                                    );

                                    ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            <?php endif; ?>

            <hr>

            <p>

                <?php if (
                    $commission['status'] === CommissionManager::STATUS_PENDING
                ) : ?>

                <form method="post">

                    <?php
                    wp_nonce_field(
                        'mediaa_mark_paid_' . $commissionId
                    );
                    ?>

                    <input
                        type="hidden"
                        name="mediaa_mark_paid"
                        value="1">

                    <button
                        type="submit"
                        class="button button-primary">

                        Przekaż do wypłaty

                    </button>

                </form>

                <?php else : ?>

                <p>

                    <span
                        style="
                            color:#008a20;
                            font-weight:600;
                        ">

                        ✔ Ta prowizja została już wypłacona.

                    </span>

                </p>

                <?php endif; ?>

            </p>

        </div>

        <?php
    }
}