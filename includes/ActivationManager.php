<?php

namespace MediaaB2B;

class ActivationManager
{
    public static function activate(): void
    {
        Roles::create();

        Pages::create();

        Database::install();

        \flush_rewrite_rules();
    }
}