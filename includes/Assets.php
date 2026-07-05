<?php

namespace MediaaB2B;

class Assets
{
    public function register(): void
    {
        \add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueue']
        );
    }

    public function enqueue(): void
    {
        if (! \is_page('b2b')) {
            return;
        }

        \wp_enqueue_script(
            'mediaa-b2b-portal',
            MEDIAA_B2B_URL . 'assets/js/portal.js',
            [],
            '1.0.0',
            true
        );
    }
}
