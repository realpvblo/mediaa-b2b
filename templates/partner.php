<?php

use MediaaB2B\DashboardController;
use MediaaB2B\CommissionManager;

$controller = new DashboardController();

$data = $controller->getPartnerDashboard();

$partnerCode = $data['code'];

$partnerLink = $data['link'];

$partnerRate = $data['rate'] . '%';

$partnerBalance = number_format_i18n(
    $data['balance'],
    2
) . ' zł';

$partnerPaid = number_format_i18n(
    $data['paid'],
    2
) . ' zł';

$partnerOrders = $data['orders'];

$commissions = $data['commissions'];

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

        <div
            class="mediaa-partner-link"
            style="margin-top:15px;"
        >
            <label>
                <strong>Twój link partnerski</strong>
            </label>

            <div class="mediaa-partner-code-box">
                <input
                    type="text"
                    value="<?php echo esc_attr(
                        $partnerLink
                    ); ?>"
                    readonly>

                <button
                    type="button"
                    class="mediaa-copy-code"
                    onclick="copyPartnerLink()"
                >
                    📋 Kopiuj
                </button>
            </div>
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

                            <td><?php echo esc_html(date_i18n(
                                'd.m.Y',
                                strtotime(
                                    $commission->created_at
                                )
                            )); ?></td>

                            <td>#<?php echo esc_html($commission->order_id); ?></td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $commission->order_total,
                                        2
                                    ) . ' zł'
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $commission->commission_amount,
                                        2
                                    ) . ' zł'
                                );
                                ?>
                            </td>

                            <?php
                            $status = '-';
                            $class = '';

                            if ($commission->status === CommissionManager::STATUS_PAID) {
                                $status = 'Wypłacono';
                                $class = 'is-paid';
                            } elseif (! empty($commission->withdrawal_id)) {
                                $status = 'Do wypłaty';
                                $class = 'is-pending';
                            }
                            ?>
                            <td>
                                <span class="mediaa-status <?php echo esc_attr($class); ?>">
                                    <?php echo esc_html($status); ?>
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

function copyPartnerLink() {

    const input = document.querySelector(
        '.mediaa-partner-link input'
    );

    navigator.clipboard.writeText(
        input.value
    );

    const button = document.querySelector(
        '.mediaa-copy-link'
    );

    const originalText = button.innerHTML;

    button.innerHTML = '✅ Skopiowano';

    setTimeout(() => {

        button.innerHTML = originalText;

    }, 2000);

}

</script>