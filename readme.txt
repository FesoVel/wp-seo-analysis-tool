=== SEO Analysis Tool ===
Contributors: mrflint
Tags: seo, keywords, content, analysis, admin
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Analyze published content by post type for word counts, keyword matches, and per-post keyword density.

== Description ==
SEO Analysis Tool scans your published posts and custom post types to report word counts, keyword matches, and per-post keyword density. It is optimized for large sites using server-side batching and an efficient admin UI.

= Features =
* Batch analysis to handle large sites
* Keyword density per post
* Works with any public post type
* Admin-only access with nonce verification

== Installation ==
1. Upload the plugin to `/wp-content/plugins/wp-seo-analysis-tool/`.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Tools → SEO Keyword Analysis.

== Frequently Asked Questions ==
= Who can run the analysis? =
Only administrators (users with the `manage_options` capability).

== Screenshots ==
1. Analysis screen with results table

== Changelog ==
= 1.1.0 =
* Add batched analysis for scalability
* Add capability checks and nonce verification
* Improve escaping and i18n

== Upgrade Notice ==
= 1.1.0 =
Security and performance improvements; please update.

