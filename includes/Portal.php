<?php

namespace MediaaB2B;

class Portal
{
    public function register(): void
    {
        \add_shortcode(
            'mediaa_b2b_portal',
            [$this, 'render']
        );
    }

    public function render(): string
    {
        if (! \is_user_logged_in()) {
            return View::render('guest');
        }

        $user = \wp_get_current_user();

        if (Roles::isPending($user)) {
            return View::render('pending');
        }

        if (Roles::isB2B($user)) {
            return View::render('dashboard');
        }

        return View::render('not-authorized');
    }
}
