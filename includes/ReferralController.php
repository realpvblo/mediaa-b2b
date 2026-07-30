<?php

namespace MediaaB2B;

if (! defined('ABSPATH')) {
    exit;
}

class ReferralController
{
    private const SESSION_PARTNER_ID = 'mediaa_partner_id';

    private const SESSION_PARTNER_CODE = 'mediaa_partner_code';

    public function register(): void
    {
        add_action(
            'template_redirect',
            [$this, 'captureReferral']
        );
    }

    public function captureReferral(): void
    {
        if (! function_exists('WC') || ! WC()->session) {
            return;
        }

        $code = sanitize_text_field(
            wp_unslash(
                $_GET['partner']
                ?? $_GET['ref']
                ?? ''
            )
        );

        if ($code === '') {
            return;
        }

        self::setPartnerByCode($code);
    }

    public static function setPartner(
        int $partnerId,
        string $partnerCode
    ): void
    {
        if (! function_exists('WC') || ! WC()->session) {
            return;
        }

        WC()->session->set(
            self::SESSION_PARTNER_ID,
            $partnerId
        );

        WC()->session->set(
            self::SESSION_PARTNER_CODE,
            strtoupper(trim($partnerCode))
        );
    }

    public static function clearPartner(): void
    {
        if (! function_exists('WC') || ! WC()->session) {
            return;
        }

        WC()->session->__unset(
            self::SESSION_PARTNER_ID
        );

        WC()->session->__unset(
            self::SESSION_PARTNER_CODE
        );
    }

    public static function getPartnerId(): ?int
    {
        if (! function_exists('WC') || ! WC()->session) {
            return null;
        }

        $partnerId = WC()->session->get(
            self::SESSION_PARTNER_ID
        );

        return $partnerId ? (int) $partnerId : null;
    }

    public static function getPartnerCode(): string
    {
        if (! function_exists('WC') || ! WC()->session) {
            return '';
        }

        if (self::getPartnerId() === null) {
            return '';
        }

        return (string) WC()->session->get(
            self::SESSION_PARTNER_CODE
        );
    }

    public static function setPartnerByCode(
    string $code
    ): bool
    {
        $code = strtoupper(
            trim(
                sanitize_text_field($code)
            )
        );

        if ($code === '') {
            return false;
        }

        $partnerId = PartnerManager::getUserIdByCode($code);

        if ($partnerId === null) {
            self::clearPartner();
            return false;
        }

        self::setPartner(
            $partnerId,
            $code
        );

        return true;
    }
    
}