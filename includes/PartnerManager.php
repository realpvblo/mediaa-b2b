<?php

namespace MediaaB2B;

class PartnerManager
{
    private const CODE_META = 'mediaa_partner_code';

    private const RATE_META = 'mediaa_partner_rate';

    public static function isPartner(int $userId): bool
    {
        return self::getCode($userId) !== '';
    }

    public static function getCode(int $userId): string
    {
        return (string) get_user_meta(
            $userId,
            self::CODE_META,
            true
        );
    }

    public static function setCode(
        int $userId,
        string $code
    ): void
    {
        update_user_meta(
            $userId,
            self::CODE_META,
            strtoupper(trim($code))
        );
    }

    public static function removeCode(int $userId): void
    {
        delete_user_meta(
            $userId,
            self::CODE_META
        );
    }

    public static function getRate(int $userId): float
    {
        return (float) get_user_meta(
            $userId,
            self::RATE_META,
            true
        );
    }

    public static function setRate(
        int $userId,
        float $rate
    ): void
    {
        $rate = max(0, min(100, $rate));

        update_user_meta(
            $userId,
            self::RATE_META,
            $rate
        );
    }

    public static function removeRate(int $userId): void
    {
        delete_user_meta(
            $userId,
            self::RATE_META
        );
    }

    public static function updatePartner(
        int $userId,
        string $code,
        float $rate
    ): void
    {
        self::setCode($userId, $code);

        self::setRate($userId, $rate);
    }

    public static function codeExists(
    string $code,
    ?int $excludeUserId = null
    ): bool {
        $code = trim($code);

        if ($code === '') {
            return false;
        }

        $users = get_users([
            'meta_key'   => self::CODE_META,
            'meta_value' => $code,
            'number'     => 1,
            'fields'     => 'ids',
        ]);

        if (empty($users)) {
            return false;
        }

        if (
            $excludeUserId !== null &&
            (int) $users[0] === $excludeUserId
        ) {
            return false;
        }

        return true;
    }

    public static function getUserIdByCode(
    string $code
    ): ?int
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return null;
        }

        $users = get_users(
            [
                'meta_key'   => self::CODE_META,
                'meta_value' => $code,
                'number'     => 1,
                'fields'     => 'ids',
            ]
        );

        if (empty($users)) {
            return null;
        }

        return (int) $users[0];
    }

    public static function getPartners(): array
    {
        $users = get_users([
            'role' => Roles::ROLE_B2B_CUSTOMER,
        ]);

        $partners = [];

        foreach ($users as $user) {

            if (! self::isPartner($user->ID)) {
                continue;
            }

            $company = (string) get_user_meta(
                $user->ID,
                'billing_company',
                true
            );

            if ($company === '') {
                $company = $user->display_name;
            }

            $partners[] = [
                'id' => $user->ID,

                'company' => $company,

                'code' => self::getCode(
                    $user->ID
                ),

                'rate' => self::getRate(
                    $user->ID
                ),

                'balance' => self::formatMoney(
                    CommissionManager::getPartnerPendingBalance(
                        $user->ID
                    )
                ),

                'paid' => self::formatMoney(
                    CommissionManager::getPartnerPaidBalance(
                        $user->ID
                    )
                ),

                'orders' => CommissionManager::getPartnerOrdersCount(
                    $user->ID
                ),
            ];
        }

        return $partners;
    }

    private static function formatMoney(
    float $amount
    ): string
    {
        return wp_strip_all_tags(
            wc_price($amount)
        );
    }

    public static function getPartnerDetails(
        int $partnerId
    ): ?array
    {
        $user = get_userdata($partnerId);

        if (! $user) {
            return null;
        }

        $company = get_user_meta(
            $partnerId,
            'billing_company',
            true
        );

        if ($company === '') {
            $company = $user->display_name;
        }

        $commissions = CommissionManager::getPartnerCommissions(
            $partnerId
        );

        return [

            'id' => $partnerId,

            'company' => $company,

            'name' => $user->display_name,

            'email' => $user->user_email,

            'phone' => get_user_meta(
                $partnerId,
                'billing_phone',
                true
            ),

            'nip' => get_user_meta(
                $partnerId,
                'billing_nip',
                true
            ),

            'code' => self::getCode(
                $partnerId
            ),

            'rate' => self::getRate(
                $partnerId
            ),

            'orders_count' => CommissionManager::getPartnerOrdersCount(
                $partnerId
            ),

            'pending_balance' => CommissionManager::getPartnerPendingBalance(
                $partnerId
            ),

            'paid_balance' => CommissionManager::getPartnerPaidBalance(
                $partnerId
            ),

            'total_balance' => CommissionManager::getPartnerTotalBalance(
                $partnerId
            ),

            'commissions' => array_slice(
                $commissions,
                0,
                5
            ),
        ];
    }

    public static function getDashboardData(
        int $partnerId
    ): array {

        $code = self::getCode($partnerId);

        return [

            'code' => $code,

            'link' => add_query_arg(
                'ref',
                $code,
                home_url('/')
            ),

            'rate' => self::getRate($partnerId),

            'balance' => CommissionManager::getPartnerPendingBalance(
                $partnerId
            ),

            'paid' => CommissionManager::getPartnerPaidBalance(
                $partnerId
            ),

            'orders' => CommissionManager::getPartnerOrdersCount(
                $partnerId
            ),

            'commissions' => CommissionManager::getPartnerCommissions(
                $partnerId
            ),

            'can_request_withdrawal' => ! empty(
                CommissionManager::getPendingPartnerCommissions(
                    $partnerId
                )
            ),

        ];
    }
}