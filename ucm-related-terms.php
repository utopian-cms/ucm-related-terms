<?php
/**
 * Plugin Name: UCM Related Terms
 * Plugin URI: https://github.com/utopian-cms/ucm-related-terms
 * Description: Adds a function and widget to get and display related terms from a different taxonomy.
 * Version: 1.0.0
 * Author: Paul Sandberg
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) exit;

// Load widget
require_once plugin_dir_path(__FILE__) . 'includes/class-ucm-related-terms-widget.php';

// Register widget
add_action('widgets_init', function() {
    register_widget('UCM_Related_Terms_Widget');
});

function ucm_get_related_terms($term_id = null, $desired_taxonomy = 'post_tag', $limit = 5) {
    $related_terms = [];
    if ($term_id)  { 
    $term = get_term($term_id);
    } else {
    $term = get_queried_object();
    $term_id = $term->term_id;
    }
    if (!$term || is_wp_error($term)) return [];

    // Get all posts with the original term
    $posts = get_posts([
        'fields' => 'ids',
        'posts_per_page' => -1,
        'tax_query' => [[
            'taxonomy' => $term->taxonomy,
            'field' => 'term_id',
            'terms' => [$term_id],
        ]]
    ]);

    if (empty($posts)) return [];

    // Get all terms of the desired taxonomy attached to those posts
    $terms = wp_get_object_terms($posts, $desired_taxonomy);
    $terms = array_filter($terms, function($t) use ($term_id) {
        return $t->term_id !== $term_id;
    });

    foreach ($terms as &$t) {
        $related_posts = get_posts([
            'fields' => 'ids',
            'posts_per_page' => -1,
            'tax_query' => [[
                'taxonomy' => $desired_taxonomy,
                'field' => 'term_id',
                'terms' => [$t->term_id],
            ]]
        ]);
        $t->similarCount = count(array_intersect($related_posts, $posts));
    }

    // Sort by similarity count
    usort($terms, function($a, $b) {
        return $b->similarCount <=> $a->similarCount;
    });

    return array_slice($terms, 0, $limit);
}


// Register REST API endpoint
add_action('rest_api_init', function () {
    register_rest_route('ucm/v1', '/related-terms', [
        'methods' => 'GET',
        'callback' => 'ucm_get_related_terms_rest',
        'args' => [
            'term_id' => ['required' => true, 'validate_callback' => 'is_numeric'],
            'taxonomy' => ['required' => true],
            'limit' => ['default' => 5, 'validate_callback' => 'is_numeric'],
        ],
        'permission_callback' => '__return_true',
    ]);
});

function ucm_get_related_terms_rest($request) {
    $term_id = intval($request['term_id']);
    $taxonomy = sanitize_text_field($request['taxonomy']);
    $limit = intval($request['limit']);

    $terms = ucm_get_related_terms($term_id, $taxonomy, $limit);

    $output = [];
    foreach ($terms as $term) {
        $output[] = [
            'id' => $term->term_id,
            'name' => $term->name,
            'slug' => $term->slug,
            'count' => $term->count,
            'link' => get_term_link($term),
        ];
    }

    return rest_ensure_response($output);
}
