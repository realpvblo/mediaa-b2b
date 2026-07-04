<?php

/**
 * Plugin Name: Mediaa B2B
 * Description: WooCommerce B2B Plugin
 * Version: 0.1.0
 * Author: Paweł Waszkiewicz
 * Text Domain: mediaa-b2b
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use MediaaB2B\Plugin;
use MediaaB2B\ActivationManager;
use MediaaB2B\DeactivationManager;

\register_activation_hook(
    __FILE__,
    [ActivationManager::class, 'activate']
);

\register_deactivation_hook(
    __FILE__,
    [DeactivationManager::class, 'deactivate']
);

$plugin = new Plugin();
$plugin->boot();
