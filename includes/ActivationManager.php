<?php

namespace MediaaB2B;

class ActivationManager
{
    public static function activate(): void
    {
        Roles::create();

        Pages::create();

        \flush_rewrite_rules();
    }
}
