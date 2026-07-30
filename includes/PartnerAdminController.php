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

    private function renderPartnerDetails(): void
    {
        $partnerId = absint(
            wp_unslash(
                $_GET['partner_id']
                ?? 0
            )
        );

        $partner = PartnerManager::getPartnerDetails(
            $partnerId
        );

        if (! $partner) {

            wp_die(
                esc_html__(
                    'Partner nie istnieje.',
                    'mediaa-b2b'
                )
            );

        }

        echo View::render(
            'admin/partner-details',
            [
                'partner' => $partner,
            ]
        );
    }

    public function renderPartnerPage(): void
    {
        $action = sanitize_key(
            wp_unslash(
                $_GET['action']
                ?? ''
            )
        );

        if ($action === 'view') {

            $this->renderPartnerDetails();

            return;
        }

        $partners = PartnerManager::getPartners();

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
                                <?php echo esc_html(
                                    $partner['rate']
                                ); ?>%
                            </td>

                            <td><?php echo esc_html($partner['balance']); ?></td>

                            <td><?php echo esc_html($partner['paid']); ?></td>

                            <td><?php echo esc_html($partner['orders']); ?></td>

                            <td>
                                <a
                                    href="<?php echo esc_url(
                                        admin_url(
                                            'admin.php?page=mediaa-b2b-partners'
                                            . '&action=view'
                                            . '&partner_id='
                                            . $partner['id']
                                        )
                                    ); ?>"
                                    class="button button-primary"
                                >
                                    Zobacz szczegóły
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

        <?php
    }
}