<?php
/**
 * Plugin Name: SEO Analysis Tool
 * Description: Analyze published content for word count and keyword density, with frontend display and REST API support.
 * Version: 1.1.0
 * Author: Viktor Veljanovski
 * Text Domain: wp-seo-analysis-tool
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

include_once plugin_dir_path(__FILE__) . 'includes/activation_deactivation.php';
register_activation_hook(__FILE__, 'sat_activate');
register_deactivation_hook(__FILE__, 'sat_deactivate');

// Load text domain for translations
add_action('plugins_loaded', function() {
    load_plugin_textdomain('wp-seo-analysis-tool', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

include_once plugin_dir_path(__FILE__) . 'includes/options_page.php';
include_once plugin_dir_path(__FILE__) . 'includes/ajax.php';
include_once plugin_dir_path(__FILE__) . 'includes/enqueues.php';