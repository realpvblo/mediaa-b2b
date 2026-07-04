<?php

namespace MediaaB2B;

class Portal
{
    public function register(): void
    {
        \add_shortcode(
            'mediaaa_b2b_portal',
            [$this, 'render']
        );
    }

    public function render(): string
    {
        ob_start();

        include dirname(__DIR__) . '/templates/portal.php';

        return ob_get_clean();
    }
}
