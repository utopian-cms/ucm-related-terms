=== UCM Related Terms ===
Contributors: utopian-cms
Tags: related, terms, tags, widget
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
Requires PHP: 7.4


== Description ==
A simple WordPress plugin that provides:

- A function `ucm_get_related_terms($term_id, $taxonomy, $limit)` to get the most related terms from a different taxonomy.
- A widget to display related terms in the sidebar.
- A REST API endpoint for use with JavaScript/front-end frameworks.

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/ucm-related-terms/`
2. Activate via WP Admin → Plugins
3. Use the function, widget, or API as needed

Usage:

`$related = ucm_get_related_terms(23, 'category', 5)`

