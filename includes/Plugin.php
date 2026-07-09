<?php

namespace MediaaB2B;

class Plugin
{
    public function boot(): void
    {
        (new Assets())->register();

        // Register plugin modules.
        (new Registration())->register();

        (new Portal())->register();

        (new AuthController())->register();

        (new ProductManager())->register();

        (new AdminController())->register();
    }
}
