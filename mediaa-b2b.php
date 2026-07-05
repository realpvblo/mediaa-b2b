<?php

/**
 * Plugin Name: Mediaa B2B
 * Description: WooCommerce B2B Plugin
 * Version: 0.2.0
 * Author: Paweł Waszkiewicz
 * Text Domain: mediaa-b2b
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin constants.
 */
define('MEDIAA_B2B_VERSION', '0.1.0');
define('MEDIAA_B2B_PATH', plugin_dir_path(__FILE__));
define('MEDIAA_B2B_URL', plugin_dir_url(__FILE__));

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
