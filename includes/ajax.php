<?php
// Add AJAX action for logged-in users
add_action('wp_ajax_sat_analyze_keyword', 'sat_analyze_keyword');
function sat_analyze_keyword() {
    $keyword = sanitize_text_field($_POST['keyword']);
    $post_type = sanitize_text_field($_POST['post_type']);
    
    $args = array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids', // return only IDs to reduce memory
        'no_found_rows'  => true,
        'suppress_filters' => false,
    );
    $post_ids = get_posts($args);
    $json_results = array();
    

    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);
        if (!$post) { continue; }
        $content = apply_filters('the_content', $post->post_content);
        $plain_text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($content)));
        $word_count = $plain_text === '' ? 0 : count(explode(' ', $plain_text));
        preg_match_all('/\b' . preg_quote($keyword, '/') . '\b/i', $plain_text, $matches);
        $post_keyword_count = count($matches[0]);
        $keyword_density = ($word_count > 0) ? (($post_keyword_count / $word_count) * 100) : 0;
        
        $json_results[] = array(
            '<a href="' . esc_url(get_permalink($post_id)) . '" target="_blank">' . esc_html($post->post_title) . '</a>',
            $word_count,
            $post_keyword_count,
            number_format($keyword_density, 2) . "%",
        );
    }
    wp_send_json($json_results);
}