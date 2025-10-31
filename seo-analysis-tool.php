<?php
/**
 * Plugin Name: SEO Analysis Tool
 * Description: Analyze published content for word count and keyword density, with frontend display and REST API support.
 * Version: 1.1.0
 * Author: Viktor Veljanovski
 * Text Domain: wp-post-analysis-tool
 */

include_once plugin_dir_path(__FILE__) . 'includes/activation_deactivation.php';
register_activation_hook(__FILE__, 'sat_activate');
register_deactivation_hook(__FILE__, 'sat_deactivate');

include_once plugin_dir_path(__FILE__) . 'includes/options_page.php';

include_once plugin_dir_path(__FILE__) . 'includes/ajax.php';

include_once plugin_dir_path(__FILE__) . 'includes/enqueues.php';