<?php
// Add AJAX action for logged-in users
add_action('wp_ajax_sat_analyze_keyword', 'sat_analyze_keyword');
function sat_analyze_keyword() {
    $keyword = sanitize_text_field($_POST['keyword']);
    $post_type = sanitize_text_field($_POST['post_type']);
    
    $args = array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'numberposts' => -1
    );
    $posts = get_posts($args);
    $json_results = array();
    $total_word_count = 0;
    $keyword_count = 0;

    foreach ($posts as $post) {
        $content = apply_filters('the_content', $post->post_content);
        $word_count = count(explode(" ", strip_tags($content)));
        $total_word_count += $word_count;
        preg_match_all('/\b' . preg_quote(strtolower($keyword), '/') . '\b/i', $content, $matches);
        $keyword_count += count($matches[0]);
        $keyword_density = ($word_count > 0) ? (($keyword_count / $total_word_count) * 100) : 0;
        
        $json_results[] = array(
            '<a href="'.get_permalink($post->ID).'" target="_blank">'. $post->post_title . '</a>',
            $word_count,
            $keyword_count,
            number_format($keyword_density, 2) . "%",
        );
        $keyword_count = 0;
    }
    $keyword_density = $total_word_count > 0 ? ($keyword_count / $total_word_count) * 100 : 0; // This line may need adjustment based on your requirements
    $results = json_encode($json_results);

    echo $results;
    wp_die();
}