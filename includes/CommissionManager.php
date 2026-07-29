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

    public static function getCommissionByOrderId(
    int $orderId
    ): ?object
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        $commission = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table}
                WHERE order_id = %d
                LIMIT 1
                ",
                $orderId
            )
        );

        return $commission;
    }

    public static function getCommissionById(
    int $commissionId
    ): ?object
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        $commission = $wpdb->get_row(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table}
                WHERE id = %d
                LIMIT 1
                ",
                $commissionId
            )
        );

        return $commission;
    }

    public static function getPartnerCommissions(
    int $partnerId
    ): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table}
                WHERE partner_id = %d
                ORDER BY created_at DESC
                ",
                $partnerId
            )
        );
    }

    public static function getPendingCommissions(
    int $partnerId
    ): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT *
                FROM {$table}
                WHERE partner_id = %d
                AND status = 'pending'
                ORDER BY created_at ASC
                ",
                $partnerId
            )
        );
    }

    public static function getAllCommissions(): array
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        return $wpdb->get_results(
            "
            SELECT *
            FROM {$table}
            ORDER BY created_at DESC
            "
        );
    }

    public static function getCommissionList(): array
    {
        $rows = self::getAllCommissions();

        $commissions = [];

        foreach ($rows as $row) {

            $user = get_userdata(
                $row->partner_id
            );

            $company = get_user_meta(
                $row->partner_id,
                'billing_company',
                true
            );

            if ($company === '') {
                $company = $user?->display_name ?? '-';
            }

            $commissions[] = [

                'id' => (int) $row->id,

                'partner_id' => (int) $row->partner_id,

                'partner' => $company,

                'code' => PartnerManager::getCode(
                    $row->partner_id
                ),

                'order_id' => (int) $row->order_id,

                'order_total' => wc_price(
                    $row->order_total
                ),

                'commission' => wc_price(
                    $row->commission_amount
                ),

                'rate' => (float) $row->commission_rate,

                'status' => $row->status,

                'created_at' => $row->created_at,
            ];
        }

        return $commissions;
    }

    public static function getCommissionDetails(
    int $commissionId
    ): ?array
    {
        $commission = self::getCommissionById(
            $commissionId
        );

        if (! $commission) {
            return null;
        }

        $user = get_userdata(
            $commission->partner_id
        );

        $company = get_user_meta(
            $commission->partner_id,
            'billing_company',
            true
        );

        if ($company === '') {
            $company = $user?->display_name ?? '-';
        }

        $order = wc_get_order(
            $commission->order_id
        );

        return [

            'id' => (int) $commission->id,

            'partner_id' => (int) $commission->partner_id,

            'partner' => $company,

            'code' => PartnerManager::getCode(
                $commission->partner_id
            ),

            'rate' => (float) $commission->commission_rate,

            'status' => $commission->status,

            'created_at' => $commission->created_at,

            'order_id' => (int) $commission->order_id,

            'order' => $order,

            'order_total' => wc_price(
                $commission->order_total
            ),

            'commission' => wc_price(
                $commission->commission_amount
            ),

            'commission_amount' => (float) $commission->commission_amount,
        ];
    }

    public static function getPartnerPendingBalance(
    int $partnerId
    ): float
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        $amount = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT SUM(commission_amount)
                FROM {$table}
                WHERE partner_id = %d
                AND status = 'pending'
                ",
                $partnerId
            )
        );

        return (float) ($amount ?? 0);
    }

    public static function getPartnerPaidBalance(
    int $partnerId
    ): float
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        $amount = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT SUM(commission_amount)
                FROM {$table}
                WHERE partner_id = %d
                AND status = 'paid'
                ",
                $partnerId
            )
        );

        return (float) ($amount ?? 0);
    }

    public static function getPartnerTotalBalance(
    int $partnerId
    ): float
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        $amount = $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT SUM(commission_amount)
                FROM {$table}
                WHERE partner_id = %d
                ",
                $partnerId
            )
        );

        return (float) ($amount ?? 0);
    }

    public static function getPartnerOrdersCount(
    int $partnerId
    ): int
    {
        global $wpdb;

        $table = $wpdb->prefix . 'mediaa_b2b_commissions';

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "
                SELECT COUNT(*)
                FROM {$table}
                WHERE partner_id = %d
                ",
                $partnerId
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