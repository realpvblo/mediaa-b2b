<?php

namespace MediaaB2B;

class ActivationManager
{
    public static function activate(): void
    {
        \flush_rewrite_rules();
    }
}
