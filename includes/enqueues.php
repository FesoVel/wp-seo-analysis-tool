<?php
defined('ABSPATH') || exit;
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

function wpdocs_enqueue_custom_admin_style() {
    global $pagenow;

    // Only enqueue on Tools -> SEO Keyword Analysis admin page
    $is_tools_page = ($pagenow === 'tools.php');
    $is_plugin_page = isset($_GET['page']) && $_GET['page'] === 'seo-analysis-tool';
    if (!$is_tools_page || !$is_plugin_page) {
        return;
    }
    wp_register_style( 'custom_wp_admin_css', plugin_dir_url(__DIR__) . 'assets/css/dataTables.dataTables.min.css', false, false );
    wp_enqueue_style( 'custom_wp_admin_css' );

    wp_enqueue_script('sat-script', plugin_dir_url(__DIR__) . 'assets/js/dataTables.min.js', array('jquery'), null, true);
}