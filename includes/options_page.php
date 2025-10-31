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
        <h1><?php _e('Site-wide SEO Content Analysis', 'wp-post-analysis-tool'); ?></h1>
        <p><?php _e('Analyze published content by post type. View word counts, keyword matches, and per-post keyword density. Select a post type, enter a focus keyword, and start the analysis.', 'wp-post-analysis-tool'); ?></p>
        <form id="sat-keyword-form">
            <h4><?php _e('Select a post type:', 'wp-post-analysis-tool') ?></h4>
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
                var page = 0;
                var perPage = 500; // batch size
                var total = 0;
                var processed = 0;
                var tableInstance = null;

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

                function updateLoader() {
                    if (total > 0) {
                        loader.text('Analyzing... ' + processed + ' / ' + total);
                    } else {
                        loader.text('Analyzing...');
                    }
                }

                function ensureTable() {
                    if (tableInstance) { return tableInstance; }
                    tableInstance = $('#sat-analysis-results').DataTable({
                        columns: [
                            { title: 'Post Title' },
                            { title: 'Word Count' },
                            { title: 'Keyword Count' },
                            { title: 'Keyword Density' },
                        ],
                        data: [],
                        stateSave: true,
                        bDestroy: true,
                        deferRender: true,
                    });
                    return tableInstance;
                }

                function fetchBatch() {
                    updateLoader();
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'sat_analyze_keyword',
                            keyword: keyword,
                            post_type: postType,
                            page: page,
                            per_page: perPage
                        },
                        success: function(response) {
                            var meta = response.meta || {};
                            total = meta.total || total;
                            processed = meta.processed || processed;
                            var rows = response.rows || [];

                            var dt = ensureTable();
                            if (rows.length) {
                                dt.rows.add(rows).draw(false);
                            }

                            if (meta.has_more) {
                                page += 1;
                                fetchBatch();
                            } else {
                                loader.remove();
                            }
                        },
                        error: function() {
                            $('#sat-analysis-results').html('An error occurred. Please try again.');
                            loader.remove();
                        }
                    });
                }

                fetchBatch();
            });
        });
    </script>
    <?php
}