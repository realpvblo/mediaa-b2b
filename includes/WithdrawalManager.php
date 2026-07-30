<?php

namespace MediaaB2B;

if (! defined('ABSPATH')) {
    exit;
}

class WithdrawalManager
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public static function getTableName(): string
    {
        global $wpdb;
        return $wpdb->prefix . 'mediaa_b2b_withdrawals';
    }

    public static function getById(int $withdrawalId): ?object
    {
        global $wpdb;

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . self::getTableName() . " WHERE id = %d",
                $withdrawalId
            )
        );
    }

    public static function getPartnerWithdrawals(
        int $partnerId
    ): array {
        global $wpdb;

        return $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT *
                FROM " . self::getTableName() . "
                WHERE partner_id = %d
                ORDER BY requested_at DESC
                ",
                $partnerId
            )
        );
    }

    public static function createFromCommission(
        int $commissionId,
        ?string $note = null
    ): ?int {

        global $wpdb;

        $commission = CommissionManager::getCommissionById(
            $commissionId
        );

        if (! $commission) {
            return null;
        }

        if (! empty($commission->withdrawal_id)) {
            return (int) $commission->withdrawal_id;
        }

        $inserted = $wpdb->insert(
            self::getTableName(),
            [
                'partner_id' => $commission->partner_id,

                'amount' => $commission->commission_amount,

                'status' => self::STATUS_PENDING,

                'requested_at' => current_time('mysql'),

                'processed_at' => null,

                'processed_by' => null,

                'note' => $note,
            ],
            [
                '%d',
                '%f',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            ]
        );

        if ($inserted === false) {
            return null;
        }

        $withdrawalId = (int) $wpdb->insert_id;

        $wpdb->update(
            $wpdb->prefix . 'mediaa_b2b_commissions',
            [
                'withdrawal_id' => $withdrawalId,
            ],
            [
                'id' => $commissionId,
            ],
            [
                '%d',
            ],
            [
                '%d',
            ]
        );

        return $withdrawalId;
    }

    public static function getAll(): array
    {
        global $wpdb;

        return $wpdb->get_results(
            "
            SELECT *
            FROM " . self::getTableName() . "
            ORDER BY requested_at DESC
            "
        );
    }

    public static function getList(): array
    {
        $withdrawals = self::getAll();

        $rows = [];

        foreach ($withdrawals as $withdrawal) {

            $user = get_userdata(
                $withdrawal->partner_id
            );

            $company = get_user_meta(
                $withdrawal->partner_id,
                'billing_company',
                true
            );

            if ($company === '') {
                $company = $user?->display_name ?? '-';
            }

            $rows[] = [

                'id' => (int) $withdrawal->id,

                'partner' => $company,

                'partner_id' => (int) $withdrawal->partner_id,

                'amount' => wc_price(
                    $withdrawal->amount
                ),

                'status' => $withdrawal->status,

                'requested_at' => $withdrawal->requested_at,

                'processed_at' => $withdrawal->processed_at,

                'processed_by' => $withdrawal->processed_by,
            ];
        }

        return $rows;
    }

    public static function markAsPaid(
    int $withdrawalId
    ): bool
    {
        global $wpdb;

        $withdrawal = self::getById(
            $withdrawalId
        );

        if (! $withdrawal) {
            return false;
        }

        if ($withdrawal->status === self::STATUS_PAID) {
            return true;
        }

        $updated = $wpdb->update(
            self::getTableName(),
            [
                'status' => self::STATUS_PAID,
                'processed_at' => current_time('mysql'),
                'processed_by' => get_current_user_id(),
            ],
            [
                'id' => $withdrawalId,
            ],
            [
                '%s',
                '%s',
                '%d',
            ],
            [
                '%d',
            ]
        );

        if ($updated === false) {
            return false;
        }

        $wpdb->update(
            CommissionManager::getTableName(),
            [
                'status' => CommissionManager::STATUS_PAID,
                'paid_at' => current_time('mysql'),
            ],
            [
                'withdrawal_id' => $withdrawalId,
            ],
            [
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );

        return true;
    }
}