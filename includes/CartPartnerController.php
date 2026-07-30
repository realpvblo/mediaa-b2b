<?php

namespace MediaaB2B;
use WC_Order;

if (! defined('ABSPATH')) {
    exit;
}

class CartPartnerController
{
    public function renderPartnerNotice(): void
    {
        $partnerCode = ReferralController::getPartnerCode();

        if ($partnerCode === '') {
            return;
        }

        wc_print_notice(
            sprintf(
                'Polecony przez partnera: <strong>%s</strong>',
                esc_html($partnerCode)
            ),
            'success'
        );
    }

    public function renderPartnerForm(): void
    {
        $partnerCode = ReferralController::getPartnerCode();

        if ($partnerCode !== '') {
        ?>
        
        <div class="mediaa-b2b-partner-form">

            <p>
                <strong>Partner:</strong>
                <?php echo esc_html($partnerCode); ?>
            </p>

            <form method="post">

                <?php
                wp_nonce_field(
                    'mediaa_b2b_remove_partner',
                    'mediaa_b2b_remove_partner_nonce'
                );
                ?>

                <button
                    type="submit"
                    name="mediaa_b2b_remove_partner"
                    class="button">

                    Usuń partnera

                </button>

            </form>

        </div>

        <?php

        return;
        }
        ?>

        <div class="mediaa-b2b-partner-form">

            <form method="post">

                <?php

                wp_nonce_field(

                    'mediaa_b2b_partner',

                    'mediaa_b2b_partner_nonce'

                );

                ?>

                <input

                    type="text"

                    name="partner_code"

                    placeholder="Kod partnera">

                <button

                    type="submit"

                    name="mediaa_b2b_set_partner"

                    class="button">

                    Przypisz partnera

                </button>

            </form>

        </div>

        <?php
    }

    public function handlePartnerForm(): void
    {
        if (! isset($_POST['mediaa_b2b_set_partner'])) {
            return;
        }

        if (
            ! isset($_POST['mediaa_b2b_partner_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash(
                        $_POST['mediaa_b2b_partner_nonce']
                    )
                ),
                'mediaa_b2b_partner'
            )
        ) {
            return;
        }

        $partnerCode = sanitize_text_field(
            wp_unslash(
                $_POST['partner_code'] ?? ''
            )
        );

        if (
            ReferralController::setPartnerByCode(
                $partnerCode
            )
        ) {
            wc_add_notice(
                'Partner został przypisany.',
                'success'
            );
        } else {
            wc_add_notice(
                'Nie znaleziono partnera.',
                'error'
            );
        }

        wp_safe_redirect(
            wc_get_cart_url()
        );

        exit;
    }

    public function register(): void
    {
        add_action(
            'woocommerce_before_cart',
            [$this, 'renderPartnerNotice']
        );

        add_action(
            'woocommerce_cart_coupon',
            [$this, 'renderPartnerForm']
        );

        add_action(
            'wp',
            [$this, 'handlePartnerForm']
        );

        add_action(
            'wp',
            [$this, 'handleRemovePartner']
        );

        add_action(
            'woocommerce_checkout_create_order',
            [$this, 'savePartnerToOrder'],
            10,
            2
        );
    }

    public function handleRemovePartner(): void
    {
        if (! isset($_POST['mediaa_b2b_remove_partner'])) {
            return;
        }

        if (
            ! isset($_POST['mediaa_b2b_remove_partner_nonce'])
            || ! wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash(
                        $_POST['mediaa_b2b_remove_partner_nonce']
                    )
                ),
                'mediaa_b2b_remove_partner'
            )
        ) {
            return;
        }

        ReferralController::clearPartner();

        wc_add_notice(
            'Partner został usunięty.',
            'success'
        );

        wp_safe_redirect(
            wc_get_cart_url()
        );

        exit;
    }

    public function savePartnerToOrder(
    WC_Order $order,
    array $data
    ): void
    {
        $partnerId = ReferralController::getPartnerId();
        $partnerCode = ReferralController::getPartnerCode();

        error_log(sprintf(
            'CartPartnerController: partnerId=%s, partnerCode=%s',
            var_export($partnerId, true),
            var_export($partnerCode, true)
        ));

        if (
            $partnerId === null
            || $partnerCode === ''
        ) {
            error_log('CartPartnerController: partner not saved.');
            return;
        }

        $order->update_meta_data(
            '_mediaa_partner_id',
            $partnerId
        );

        $order->update_meta_data(
            '_mediaa_partner_code',
            $partnerCode
        );

        error_log('CartPartnerController: partner saved to order.');
    }
}