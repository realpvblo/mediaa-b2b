<?php

namespace MediaaB2B;

class DeactivationManager
{
    public static function deactivate(): void
    {
        \flush_rewrite_rules();
    }
}
