<?php
add_action( 'admin_enqueue_scripts', 'wpdocs_enqueue_custom_admin_style' );

function wpdocs_enqueue_custom_admin_style() {
    global $pagenow;

	if ($pagenow != 'tools.php' && get_query_var('page') != "seo-analysis-tool") {
		return;
	}
    wp_register_style( 'custom_wp_admin_css', plugin_dir_url(__DIR__) . 'css/dataTables.dataTables.min.css', false, false );
    wp_enqueue_style( 'custom_wp_admin_css' );

    wp_enqueue_script('sat-script', plugin_dir_url(__DIR__) . 'js/dataTables.min.js', array('jquery'), null, true);
}