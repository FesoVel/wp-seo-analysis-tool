<?php
/**
 * Plugin Name: SEO Analysis Tool
 * Description: Analyze published content for word count and keyword density, with frontend display and REST API support.
 * Version: 1.2.0
 * Author: Viktor Veljanovski
 * Text Domain: wp-seo-analysis-tool
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;

if (!defined('SAT_VERSION')) {
    define('SAT_VERSION', '1.1.0');
}

include_once plugin_dir_path(__FILE__) . 'includes/activation_deactivation.php';
register_activation_hook(__FILE__, 'sat_activate');
register_deactivation_hook(__FILE__, 'sat_deactivate');

// Note: Since WordPress 4.6, translations for plugins hosted on WP.org
// are loaded automatically and do not require load_plugin_textdomain().

include_once plugin_dir_path(__FILE__) . 'includes/options_page.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
include_once plugin_dir_path(__FILE__) . 'includes/enqueues.php';