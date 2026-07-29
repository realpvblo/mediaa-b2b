<?php

namespace MediaaB2B;

if (! defined('ABSPATH')) {
    exit;
}

class CommissionManager
{
    public function register(): void
    {
        add_action(
            'woocommerce_order_status_completed',
            [$this, 'createCommission']
        );
    }

    public function createCommission(
    int $orderId
    ): void
    {
        $order = wc_get_order($orderId);

        if (! $order) {
            return;
        }

        $partnerId = (int) $order->get_meta(
            '_mediaa_partner_id'
        );

        $partnerCode = (string) $order->get_meta(
            '_mediaa_partner_code'
        );

        if (
            $partnerId === 0
            || $partnerCode === ''
        ) {
            return;
        }

        error_log(
            sprintf(
                'CommissionManager: order=%d partner=%d code=%s',
                $orderId,
                $partnerId,
                $partnerCode
            )
        );

        $commissionRate = PartnerManager::getPartnerRate(
            $partnerId
        );

        if ($commissionRate <= 0) {
            return;
        }

        $orderTotal = (float) $order->get_total();

        $commissionAmount = round(
            $orderTotal * ($commissionRate / 100),
            2
        );

        error_log(
            sprintf(
                'Commission: %.2f PLN (%s%%) from %.2f PLN',
                $commissionAmount,
                $commissionRate,
                $orderTotal
            )
        );
    }
}