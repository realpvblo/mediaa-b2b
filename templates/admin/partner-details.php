<?php

namespace MediaaB2B;
use MediaaB2B\CommissionManager;

if (! defined('ABSPATH')) {
    exit;
}

$statusLabels = [
    CommissionManager::STATUS_PENDING => 'Oczekuje',
    CommissionManager::STATUS_PAID    => 'Wypłacona',
];

?>

<div class="wrap">

    <h1>
        Partner:
        <?php echo esc_html(
            $partner['company']
        ); ?>
    </h1>

    <p>
        <a
            href="<?php echo esc_url(
                admin_url(
                    'admin.php?page=mediaa-b2b-partners'
                )
            ); ?>"
            class="button"
        >
            ← Powrót do partnerów
        </a>
    </p>

    <table class="form-table">

        <tbody>

            <tr>
                <th>Kod partnerski</th>
                <td>
                    <input
                        type="text"
                        readonly
                        class="regular-text"
                        value="<?php echo esc_attr(
                            $partner['code']
                        ); ?>"
                        onclick="this.select();"
                    />
                </td>
            </tr>

            <tr>
                <th>Prowizja</th>
                <td>
                    <?php echo esc_html(
                        $partner['rate']
                    ); ?>%
                </td>
            </tr>

            <tr>
                <th>Firma</th>
                <td>
                    <?php echo esc_html(
                        $partner['company']
                    ); ?>
                </td>
            </tr>

            <tr>
                <th>Osoba kontaktowa</th>
                <td>
                    <?php echo esc_html(
                        $partner['name']
                    ); ?>
                </td>
            </tr>

            <tr>
                <th>E-mail</th>
                <td>
                    <a href="mailto:<?php echo esc_attr(
                        $partner['email']
                    ); ?>">
                        <?php echo esc_html(
                            $partner['email']
                        ); ?>
                    </a>
                </td>
            </tr>

            <tr>
                <th>Telefon</th>
                <td>
                    <?php echo esc_html(
                        $partner['phone']
                    ); ?>
                </td>
            </tr>

            <tr>
                <th>NIP</th>
                <td>
                    <?php echo esc_html(
                        $partner['nip']
                    ); ?>
                </td>
            </tr>

        </tbody>

    </table>

    <hr>

    <h2>Statystyki</h2>

    <table class="widefat striped">

        <tbody>

            <tr>
                <td>Liczba zamówień</td>
                <td>
                    <?php echo esc_html(
                        $partner['orders_count']
                    ); ?>
                </td>
            </tr>

            <tr>
                <td>Saldo oczekujące</td>
                <td>
                    <?php echo wc_price(
                        $partner['pending_balance']
                    ); ?>
                </td>
            </tr>

            <tr>
                <td>Saldo wypłacone</td>
                <td>
                    <?php echo wc_price(
                        $partner['paid_balance']
                    ); ?>
                </td>
            </tr>

            <tr>
                <td>Łącznie zarobiono</td>
                <td>
                    <strong>
                        <?php echo wc_price(
                            $partner['total_balance']
                        ); ?>
                    </strong>
                </td>
            </tr>

        </tbody>

    </table>

    <hr>

    <h2>Ostatnie prowizje</h2>

    <?php if (empty($partner['commissions'])) : ?>

        <p>
            Partner nie posiada jeszcze prowizji.
        </p>

    <?php else : ?>

        <table class="widefat striped">

            <thead>

                <tr>

                    <th>Zamówienie</th>

                    <th>Prowizja</th>

                    <th>Status</th>

                    <th>Data</th>

                </tr>

            </thead>

            <tbody>

                <?php foreach ($partner['commissions'] as $commission) : ?>

                    <tr>

                        <td>

                            <a href="<?php echo esc_url(
                                admin_url(
                                    'post.php?post=' .
                                    $commission->order_id .
                                    '&action=edit'
                                )
                            ); ?>">

                                #<?php echo esc_html(
                                    $commission->order_id
                                ); ?>

                            </a>

                        </td>

                        <td>

                            <?php echo wc_price(
                                $commission->commission_amount
                            ); ?>

                        </td>

                        <td>

                            <?php echo esc_html(
                                $statusLabels[$commission->status]
                                ?? $commission->status
                            ); ?>

                        </td>

                        <td>

                            <?php echo esc_html(
                                $commission->created_at
                            ); ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif; ?>

</div>