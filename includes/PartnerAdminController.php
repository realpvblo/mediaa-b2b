<?php

namespace MediaaB2B;

if (!defined('ABSPATH')) {
    exit;
}

class PartnerAdminController
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
            'Partnerzy',
            'Partnerzy',
            'manage_options',
            'mediaa-b2b-partners',
            [$this, 'renderPartnerPage']
        );
    }

    public function renderPartnerPage(): void
    {
        $partners = [

            [
                'company' => 'testB2B',
                'code' => 'TEST-PARTNER',
                'rate' => '10%',
                'balance' => '326,90 zł',
                'paid' => '1 248,30 zł',
                'orders' => 12,
            ],

        ];

        $history = [

            [
                'date' => '20.07.2026',
                'order' => '#1068',
                'customer' => 'Jan Kowalski',
                'total' => '329,00 zł',
                'commission' => '32,90 zł',
                'status' => 'Do wypłaty',
            ],

            [
                'date' => '18.07.2026',
                'order' => '#1063',
                'customer' => 'Anna Nowak',
                'total' => '599,00 zł',
                'commission' => '59,90 zł',
                'status' => 'Wypłacono',
            ],

            [
                'date' => '16.07.2026',
                'order' => '#1059',
                'customer' => 'Piotr Wiśniewski',
                'total' => '149,00 zł',
                'commission' => '14,90 zł',
                'status' => 'Wypłacono',
            ],

        ];

        ?>

        <div class="wrap">

            <h1>Partnerzy</h1>

            <p>
                Zarządzaj partnerami oraz monitoruj historię prowizji.
            </p>

            <h2 style="margin-top:30px;">Partnerzy</h2>

            <table class="widefat striped">

                <thead>

                    <tr>

                        <th>Partner</th>
                        <th>Kod</th>
                        <th>% prowizji</th>
                        <th>Saldo</th>
                        <th>Wypłacono</th>
                        <th>Użyć kodu</th>
                        <th>Akcja</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($partners as $partner) : ?>

                        <tr>

                            <td><?php echo esc_html($partner['company']); ?></td>

                            <td>

                                <span class="mediaa-code">

                                    <?php echo esc_html($partner['code']); ?>

                                </span>

                            </td>

                            <td>

                                <span class="mediaa-rate">

                                    <?php echo esc_html($partner['rate']); ?>

                                </span>

                            </td>

                            <td><?php echo esc_html($partner['balance']); ?></td>

                            <td><?php echo esc_html($partner['paid']); ?></td>

                            <td><?php echo esc_html($partner['orders']); ?></td>

                            <td>

                                <a
                                    href="<?php echo esc_url(
                                        admin_url(
                                            'admin.php?page=mediaa-b2b-partners&partner=' . urlencode($partner['company'])
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

            <h2 style="margin-top:40px;">Historia prowizji</h2>

            <table class="widefat striped">

                <thead>

                    <tr>

                        <th>Data</th>
                        <th>Zamówienie</th>
                        <th>Klient</th>
                        <th>Kwota</th>
                        <th>Prowizja</th>
                        <th>Status</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($history as $item) : ?>

                        <tr>

                            <td><?php echo esc_html($item['date']); ?></td>

                            <td><?php echo esc_html($item['order']); ?></td>

                            <td><?php echo esc_html($item['customer']); ?></td>

                            <td><?php echo esc_html($item['total']); ?></td>

                            <td><?php echo esc_html($item['commission']); ?></td>

                            <td>

                                <?php if ($item['status'] === 'Do wypłaty') : ?>

                                    <span class="mediaa-status is-pending">
                                        <?php echo esc_html($item['status']); ?>
                                    </span>

                                <?php else : ?>

                                    <span class="mediaa-status is-paid">
                                        <?php echo esc_html($item['status']); ?>
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php
    }
}