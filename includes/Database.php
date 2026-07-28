<?php

namespace MediaaB2B;

class Database
{
    // public static function install(): void
    // {
    //     $installedVersion = get_option(
    //         'mediaa_b2b_db_version',
    //         ''
    //     );

    //     if ($installedVersion !== MEDIAA_B2B_DB_VERSION) {

    //         self::createTables();

    //         update_option(
    //             'mediaa_b2b_db_version',
    //             MEDIAA_B2B_DB_VERSION
    //         );
    //     }
    // }

    public static function install(): void
    {
        self::createTables();

        update_option(
            'mediaa_b2b_db_version',
            MEDIAA_B2B_DB_VERSION
        );
    }

    private static function createTables(): void
    {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charsetCollate = self::getCharsetCollate();

        $tableCommissions = $wpdb->prefix . 'mediaa_b2b_commissions';

        $tableWithdrawals = $wpdb->prefix . 'mediaa_b2b_withdrawals';

        $queries = [

            "
            CREATE TABLE {$tableCommissions} (

                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

                partner_id BIGINT UNSIGNED NOT NULL,

                order_id BIGINT UNSIGNED NOT NULL,

                withdrawal_id BIGINT UNSIGNED NULL,

                commission_rate DECIMAL(5,2) NOT NULL,

                order_total DECIMAL(10,2) NOT NULL,

                commission_amount DECIMAL(10,2) NOT NULL,

                status VARCHAR(20) NOT NULL,

                created_at DATETIME NOT NULL,

                paid_at DATETIME NULL,

                note TEXT NULL,

                PRIMARY KEY (id),

                KEY partner_id (partner_id),

                KEY order_id (order_id),

                KEY withdrawal_id (withdrawal_id),

                KEY status (status)

            ) {$charsetCollate};
            ",

            "
            CREATE TABLE {$tableWithdrawals} (

                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

                partner_id BIGINT UNSIGNED NOT NULL,

                amount DECIMAL(10,2) NOT NULL,

                status VARCHAR(20) NOT NULL,

                requested_at DATETIME NOT NULL,

                processed_at DATETIME NULL,

                processed_by BIGINT UNSIGNED NULL,

                note TEXT NULL,

                PRIMARY KEY (id),

                KEY partner_id (partner_id),

                KEY status (status)

            ) {$charsetCollate};
            ",

        ];

        foreach ($queries as $query) {

            dbDelta($query);

        }
    }

    private static function getCharsetCollate(): string
    {
        global $wpdb;

        return $wpdb->get_charset_collate();
    }
}