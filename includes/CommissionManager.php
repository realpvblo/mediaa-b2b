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

        if ($this->commissionExists($orderId)) {
            error_log(
                sprintf(
                    'CommissionManager: commission already exists for order #%d.',
                    $orderId
                )
            );

            return;
        }

        if (! $this->insertCommission(
            $partnerId,
            $orderId,
            $commissionRate,
            $orderTotal,
            $commissionAmount
        )) {
            error_log(
                sprintf(
                    'CommissionManager: failed to insert commission for order #%d.',
                    $orderId
                )
            );

            return;
        }

        error_log(
            sprintf(
                'CommissionManager: commission created for order #%d.',
                $orderId
            )
        );
    }

    private function commissionExists(
    int $orderId
    ): bool
    {
        global $wpdb;

        $commissionId = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT id
                FROM {$wpdb->prefix}mediaa_b2b_commissions
                WHERE order_id = %d
                LIMIT 1
                ",
                $orderId
            )
        );

        return $commissionId !== null;
    }

    private function insertCommission(
    int $partnerId,
    int $orderId,
    float $commissionRate,
    float $orderTotal,
    float $commissionAmount
    ): bool
    {
        global $wpdb;

        $result = $wpdb->insert(
            $wpdb->prefix . 'mediaa_b2b_commissions',
            [
                'partner_id'        => $partnerId,
                'order_id'          => $orderId,
                'commission_rate'   => $commissionRate,
                'order_total'       => $orderTotal,
                'commission_amount' => $commissionAmount,
                'status'            => 'pending',
            ],
            [
                '%d',
                '%d',
                '%f',
                '%f',
                '%f',
                '%s',
            ]
        );

        return $result !== false;
    }
}