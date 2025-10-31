# SEO Analysis Tool

Analyze published content by post type for word counts, keyword matches, and per-post keyword density. Designed for large sites with batched server-side processing and a responsive admin UI.

## Manual Installation
1. Upload the folder to `wp-content/plugins/wp-seo-analysis-tool/`.
2. Activate the plugin via Plugins in WP Admin.

## Usage
1. Go to Tools → SEO Keyword Analysis
2. Select a post type and enter a focus keyword
3. Click Analyze to populate the table

## Notes
- Processes posts in batches to avoid memory spikes/timeouts
- AJAX requests require admin capability and nonce verification
- All user-facing strings are translatable (`seo-analysis-tool`)

## License
See LICENSE.
