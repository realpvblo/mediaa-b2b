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
        return match (PortalState::current()) {

            PortalState::GUEST => View::render('guest'),

            PortalState::PENDING => View::render('pending'),

            PortalState::CUSTOMER => View::render('dashboard'),

            PortalState::NOT_AUTHORIZED => View::render('not-authorized'),

            default => View::render('not-authorized'),
        };
    }
}
