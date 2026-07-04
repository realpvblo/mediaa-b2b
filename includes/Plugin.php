<?php

namespace MediaaB2B;

class Plugin
{
    public function boot(): void
    {
        // Register plugin modules.
        (new Registration())->register();

        (new Portal())->register();
    }
}
