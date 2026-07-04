<?php

namespace MediaaB2B;

class Pages
{
    public const PORTAL_SLUG = 'b2b';

    /**
     * Create required pages.
     */
    public static function create(): void
    {
        if (\get_page_by_path(self::PORTAL_SLUG)) {
            return;
        }

        \wp_insert_post([
            'post_title'   => 'B2B',
            'post_name'    => self::PORTAL_SLUG,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '[mediaa_b2b_portal]',
        ]);
    }
}
