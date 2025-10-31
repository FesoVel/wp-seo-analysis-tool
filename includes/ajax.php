<?php
// Add AJAX action for logged-in users
add_action('wp_ajax_sat_analyze_keyword', 'sat_analyze_keyword');
function sat_analyze_keyword() {
    $keyword = sanitize_text_field($_POST['keyword']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $page = isset($_POST['page']) ? max(0, intval($_POST['page'])) : 0;
    $per_page = isset($_POST['per_page']) ? max(1, min(1000, intval($_POST['per_page']))) : 500;
    
    $offset = $page * $per_page;

    $args = array(
        'post_type'        => $post_type,
        'post_status'      => 'publish',
        'posts_per_page'   => $per_page,
        'offset'           => $offset,
        'fields'           => 'ids', // return only IDs to reduce memory
        'no_found_rows'    => true,
        'suppress_filters' => false,
    );
    $post_ids = get_posts($args);
    $rows = array();
    

    // Precompile regex once
    $pattern = '/\b' . preg_quote($keyword, '/') . '\b/i';

    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);
        if (!$post) { continue; }
        // Use raw content to avoid heavy filters
        $raw = get_post_field('post_content', $post_id, 'raw');
        $plain_text = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($raw)));
        $word_count = $plain_text === '' ? 0 : count(explode(' ', $plain_text));
        preg_match_all($pattern, $plain_text, $matches);
        $post_keyword_count = count($matches[0]);
        $keyword_density = ($word_count > 0) ? (($post_keyword_count / $word_count) * 100) : 0;
        
        $rows[] = array(
            '<a href="' . esc_url(get_permalink($post_id)) . '" target="_blank">' . esc_html($post->post_title) . '</a>',
            $word_count,
            $post_keyword_count,
            number_format($keyword_density, 2) . "%",
        );
    }

    $counts = wp_count_posts($post_type);
    $total = isset($counts->publish) ? (int) $counts->publish : 0;
    $processed = $offset + count($post_ids);
    $has_more = $processed < $total;

    wp_send_json(array(
        'rows' => $rows,
        'meta' => array(
            'page' => $page,
            'per_page' => $per_page,
            'processed' => $processed,
            'total' => $total,
            'has_more' => $has_more,
        )
    ));
}