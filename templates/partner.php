<?php

/**
 * Tymczasowe dane demonstracyjne.
 * W kolejnych etapach zostaną zastąpione danymi z WooCommerce.
 */

$partnerCode = 'TEST-PARTNER';
$partnerRate = '10%';
$partnerBalance = '326,90 zł';
$partnerPaid = '1 248,30 zł';
$partnerOrders = 12;

$commissions = [

    [
        'date' => '20.07.2026',
        'order' => '1068',
        'total' => '329,00 zł',
        'commission' => '32,90 zł',
        'status' => 'Do wypłaty'
    ],

    [
        'date' => '18.07.2026',
        'order' => '1063',
        'total' => '599,00 zł',
        'commission' => '59,90 zł',
        'status' => 'Wypłacono'
    ],

    [
        'date' => '16.07.2026',
        'order' => '1059',
        'total' => '149,00 zł',
        'commission' => '14,90 zł',
        'status' => 'Wypłacono'
    ],

    [
        'date' => '14.07.2026',
        'order' => '1053',
        'total' => '79,00 zł',
        'commission' => '7,90 zł',
        'status' => 'Wypłacono'
    ]

];

?>

<div class="mediaa-partner">

    <h3>Program Partnerski</h3>

    <p class="mediaa-partner-description">
        Udostępniaj swój kod partnera, śledź prowizje oraz historię zamówień.
    </p>

    <div class="mediaa-partner-code">

        <label for="partner-code">
            <strong>Twój kod partnera</strong>
        </label>

        <div class="mediaa-partner-code-box">

            <input
                id="partner-code"
                type="text"
                value="<?php echo esc_attr($partnerCode); ?>"
                readonly>

            <button
                type="button"
                class="mediaa-copy-code"
                onclick="copyPartnerCode()">

                📋 Kopiuj

            </button>

        </div>

    </div>

    <div class="mediaa-partner-stats">

        <div class="mediaa-partner-card">

            <span>Prowizja</span>

            <strong><?php echo esc_html($partnerRate); ?></strong>

        </div>

        <div class="mediaa-partner-card">

            <span>Do wypłaty</span>

            <strong><?php echo esc_html($partnerBalance); ?></strong>

        </div>

        <div class="mediaa-partner-card">

            <span>Łącznie wypłacono</span>

            <strong><?php echo esc_html($partnerPaid); ?></strong>

        </div>

        <div class="mediaa-partner-card">

            <span>Zamówienia</span>

            <strong><?php echo esc_html($partnerOrders); ?></strong>

        </div>

    </div>

    <div class="mediaa-partner-history">

        <h4>Historia prowizji</h4>

        <table class="mediaa-table">

            <thead>

                <tr>

                    <th>Data</th>

                    <th>Zamówienie</th>

                    <th>Kwota zamówienia</th>

                    <th>Prowizja</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($commissions)) : ?>

                    <tr>

                        <td colspan="5" style="text-align:center;padding:30px;">

                            Nie masz jeszcze żadnych prowizji.

                        </td>

                    </tr>

                <?php else : ?>

                    <?php foreach ($commissions as $commission) : ?>

                        <tr>

                            <td><?php echo esc_html($commission['date']); ?></td>

                            <td>#<?php echo esc_html($commission['order']); ?></td>

                            <td><?php echo esc_html($commission['total']); ?></td>

                            <td><?php echo esc_html($commission['commission']); ?></td>

                            <td>

                                <span class="mediaa-status <?php echo $commission['status'] === 'Do wypłaty' ? 'is-pending' : 'is-paid'; ?>">

                                    <?php echo esc_html($commission['status']); ?>

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script>

function copyPartnerCode() {

    const input = document.getElementById('partner-code');

    navigator.clipboard.writeText(input.value);

    const button = document.querySelector('.mediaa-copy-code');

    const originalText = button.innerHTML;

    button.innerHTML = '✅ Skopiowano';

    setTimeout(() => {

        button.innerHTML = originalText;

    }, 2000);

}

</script>