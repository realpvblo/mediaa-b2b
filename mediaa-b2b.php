<?php

/**
 * Plugin Name: Mediaa B2B
 * Plugin URI: https://mediaa.pl
 * Description: WooCommerce B2B plugin.
 * Version: 0.1.0
 * Author: Paweł Waszkiewicz
 * Author URI: https://mediaa.pl
 * License: MIT
 * Text Domain: mediaa-b2b
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

use MediaaB2B\Plugin;

$plugin = new Plugin();
$plugin->boot();
