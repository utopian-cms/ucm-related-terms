<?php
class UCM_Related_Terms_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'ucm_related_terms_widget',
            'UCM Related Terms',
            ['description' => 'Displays terms related to the current term from a different taxonomy.']
        );
    }

    public function widget($args, $instance) {

        $term = get_queried_object();
        if (!$term->taxonomy) {
            return;
        }
        $taxonomy = $instance['taxonomy'] ?? 'category';
        $title = $instance['title'] ?? 'Related Terms';
        $limit = intval($instance['limit']) ?: 5;

        $related_terms = ucm_get_related_terms($term->term_id, $taxonomy, $limit);

        echo $args['before_widget'];
        if (!empty($title)) echo $args['before_title'] . esc_html($title) . $args['after_title'];

        if (!empty($related_terms)) {
            echo '<ul class="ucm-related-terms">';
            foreach ($related_terms as $related) {
                $url = get_term_link($related);
                echo '<li><a href="' . esc_url($url) . '">' . esc_html($related->name) . '</a></li>';
            }
            echo '</ul>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? 'Related Terms';
        $taxonomy = $instance['taxonomy'] ?? 'category';
        $limit = $instance['limit'] ?? 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_html($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>">Related Taxonomy:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('taxonomy')); ?>">
                <option value="category" <?php selected($taxonomy, 'category'); ?>>Category</option>
                <option value="post_tag" <?php selected($taxonomy, 'post_tag'); ?>>Tag</option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">Limit:</label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($limit); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['taxonomy'] = sanitize_key($new_instance['taxonomy']);
        $instance['limit'] = absint($new_instance['limit']);
        return $instance;
    }
}
