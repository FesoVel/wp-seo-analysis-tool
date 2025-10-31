<?php
defined('ABSPATH') || exit;
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

function wpdocs_enqueue_custom_admin_style() {
    // Only enqueue on Tools -> SEO Keyword Analysis admin page
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->base !== 'tools_page_seo-analysis-tool') {
        return;
    }
    wp_register_style( 'custom_wp_admin_css', plugin_dir_url(__DIR__) . 'assets/css/dataTables.dataTables.min.css', array(), defined('SAT_VERSION') ? SAT_VERSION : null );
    wp_enqueue_style( 'custom_wp_admin_css' );

    wp_enqueue_script('sat-script', plugin_dir_url(__DIR__) . 'assets/js/dataTables.min.js', array('jquery'), defined('SAT_VERSION') ? SAT_VERSION : null, true);
}