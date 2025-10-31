<?php

add_action('admin_menu', 'sat_add_options_page');
function sat_add_options_page() {
    add_management_page(
        'SEO Analysis Tool Options', // Page title
        'SEO Keyword Analysis',         // Menu title
        'manage_options',            // Capability
        'seo-analysis-tool',         // Menu slug
        'sat_render_options_page'    // Callback function
    );
}

function sat_render_options_page() {
    $post_types = get_post_types( array( 'public'   => true, ), 'names');
    $post_types = array_diff($post_types, ['attachment']);
    ?>
    <div class="sat-wrap">
        <h1><?php _e('SEO Content Analysis', 'wp-post-analysis-tool'); ?></h1>
        <p><?php _e('Scan all post types content for word count and keyword density.'); ?></p>
        <form id="sat-keyword-form">
            <h4><?php _e('Select post type:') ?></h4>
            <?php foreach ($post_types as $post_type) : 
                $counts = wp_count_posts($post_type);
                $count = isset($counts->publish) ? (int) $counts->publish : 0;
                ?>
                <label for="sat_<?= $post_type; ?>">
                    <input type="radio" id="sat_<?= $post_type; ?>" name="post_type" value="<?= $post_type; ?>" required>
                    <?= $post_type; ?> (<?= $count ?>)
                </label>
            <?php endforeach; ?>
            <br>
            <h4><label for="keyword"><?php _e('Enter Keyword:') ?></label></h4>
            <input type="text" id="keyword" name="keyword" required>
            <button type="submit">Analyze</button>
        </form>
        <table id="sat-analysis-results" class="stripe"></table>
    </div>
    <script>
        var $ = jQuery;
        $(document).ready(function($) {
            $('#sat-keyword-form').on('submit', function(e) {
                e.preventDefault();
                var keyword = $('#keyword').val();
                var postType = $('input[name="post_type"]:checked').val();
                
                var loader = $('<div id="loader">Analyzing...</div>').css({
                    'position': 'fixed',
                    'top': '50%',
                    'left': '50%',
                    'transform': 'translate(-50%, -50%)',
                    'background': '#fff',
                    'padding': '10px',
                    'border': '1px solid #ccc',
                    'z-index': '1000'
                }).appendTo('body');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'sat_analyze_keyword',
                        keyword: keyword,
                        post_type: postType,
                    },
                    success: function(response) {
                        var data = response;
                        
                        $('#sat-analysis-results').DataTable({
                            columns: [
                                { title: 'Post Title' },
                                { title: 'Word Count' },
                                { title: 'Keyword Count' },
                                { title: 'Keyword Density' },
                            ],
                            data: data,
                            stateSave: true,
                            bDestroy: true
                        });
                    },
                    error: function() {
                        $('#sat-analysis-results').html('An error occurred. Please try again.');
                    },
                    complete: function() {
                        loader.remove();
                    }
                });
            });
        });
    </script>
    <?php
}