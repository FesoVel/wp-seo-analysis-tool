<?php
defined('ABSPATH') || exit;

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
        <h1><?php esc_html_e('Site-wide SEO Content Analysis', 'wp-seo-analysis-tool'); ?></h1>
        <p><?php esc_html_e('Analyze published content by post type. View word counts, keyword matches, and per-post keyword density. Select a post type, enter a focus keyword, and start the analysis.', 'wp-seo-analysis-tool'); ?></p>
        <form id="sat-keyword-form">
            <?php wp_nonce_field('sat_analyze', 'nonce'); ?>
            <h4><?php esc_html_e('Select a post type:', 'wp-seo-analysis-tool') ?></h4>
            <?php foreach ($post_types as $post_type) : 
                $counts = wp_count_posts($post_type);
                $count = isset($counts->publish) ? (int) $counts->publish : 0;
                ?>
                <label for="sat_<?php echo esc_attr($post_type); ?>">
                    <input type="radio" id="sat_<?php echo esc_attr($post_type); ?>" name="post_type" value="<?php echo esc_attr($post_type); ?>" required>
                    <?php echo esc_html($post_type); ?> (<?php echo esc_html((string)$count); ?>)
                </label>
            <?php endforeach; ?>
            <br>
            <h4><label for="keyword"><?php esc_html_e('Enter Keyword:', 'wp-seo-analysis-tool') ?></label></h4>
            <input type="text" id="keyword" name="keyword" required>
            <button type="submit"><?php esc_html_e('Analyze', 'wp-seo-analysis-tool'); ?></button>
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
                var nonce = $('#sat-keyword-form input[name="nonce"]').val();
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
                        url: '<?php echo esc_url( admin_url('admin-ajax.php') ); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'sat_analyze_keyword',
                            keyword: keyword,
                            post_type: postType,
                            page: page,
                            per_page: perPage
                            ,nonce: nonce
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