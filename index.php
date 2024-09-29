<?php
/*
Plugin Name: New Recent Posts Select Categories By Thao Marky
Plugin URI:  https://thaomarky.com
Description: Widget to display recent posts with customizable options.
Version:     1.1
Author:      Thao Marky
Author URI:  https://thaomarky.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class New_Recent_Posts_Select_Categories_By_Thao_Marky extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'new_recent_posts_select_categories_by_thao_marky',
            __('Recent Posts Select Categories', 'text_domain'),
            array('description' => __('A Widget to display recent posts by categories.', 'text_domain'))
        );
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $category = $instance['category'];
        $number_of_posts = $instance['number_of_posts'];
        $show_date = !empty($instance['show_date']) ? (bool)$instance['show_date'] : false; // Ensure it's set
        $show_image_position = $instance['show_image_position'];
        $random_or_latest = $instance['random_or_latest'];

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $query_args = array(
            'posts_per_page' => absint($number_of_posts), // Validate input
            'category_name' => sanitize_text_field($category),
            'orderby' => $random_or_latest === 'random' ? 'rand' : 'date',
        );

        $recent_posts = new WP_Query($query_args);

        if ($recent_posts->have_posts()) {
            echo '<div class="recent-posts-widget">';

            while ($recent_posts->have_posts()) {
                $recent_posts->the_post();
                $post_title = get_the_title();
                $post_date = get_the_date();
                $post_link = get_permalink();
                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');

                echo '<div class="recent-post-item" style="display: flex; align-items: flex-start; margin-bottom: 15px;">';

                // Display image based on position
                if ($show_image_position === 'left' && $thumbnail_url) {
                    echo '<a href="' . esc_url($post_link) . '" style="flex-shrink: 0; width: 80px;"><img src="' . esc_url($thumbnail_url) . '" style="width: 80px; height: 80px; object-fit: cover;"></a>';
                }

                // Display title and date
                echo '<div style="padding-left: 10px; width: 100%;">'; // Set width to 100%
                echo '<a href="' . esc_url($post_link) . '" style="font-size: 14px; font-weight: bold; display: block;">' . esc_html($post_title) . '</a>';
                if ($show_date) {
                    echo '<div style="font-size: 10px; color: #666;">' . esc_html($post_date) . '</div>';
                }
                echo '</div>';

                // Display image on the right if position is set to right
                if ($show_image_position === 'right' && $thumbnail_url) {
                    echo '<a href="' . esc_url($post_link) . '" style="flex-shrink: 0; width: 80px;"><img src="' . esc_url($thumbnail_url) . '" style="width: 80px; height: 80px; object-fit: cover;"></a>';
                }

                echo '</div>';
            }

            echo '</div>';
        } else {
            echo __('No posts found.', 'text_domain');
        }

        wp_reset_postdata();
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Recent Posts', 'text_domain');
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $number_of_posts = !empty($instance['number_of_posts']) ? $instance['number_of_posts'] : 5;
        $show_date = !empty($instance['show_date']) ? (bool)$instance['show_date'] : false; // Ensure it's set
        $show_image_position = !empty($instance['show_image_position']) ? $instance['show_image_position'] : 'left';
        $random_or_latest = !empty($instance['random_or_latest']) ? $instance['random_or_latest'] : 'latest';

        // Get categories
        $categories = get_categories();
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'text_domain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Category:', 'text_domain'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" class="widefat">
                <option value=""><?php _e('All Categories', 'text_domain'); ?></option>
                <?php foreach ($categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($category, $cat->slug); ?>><?php echo esc_html($cat->name); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>"><?php _e('Number of posts to show:', 'text_domain'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_posts')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_of_posts); ?>" />
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo esc_attr($this->get_field_id('show_date')); ?>" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_date')); ?>"><?php _e('Display post date?', 'text_domain'); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('show_image_position')); ?>"><?php _e('Image Position:', 'text_domain'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('show_image_position')); ?>" name="<?php echo esc_attr($this->get_field_name('show_image_position')); ?>" class="widefat">
                <option value="left" <?php selected($show_image_position, 'left'); ?>><?php _e('Left', 'text_domain'); ?></option>
                <option value="right" <?php selected($show_image_position, 'right'); ?>><?php _e('Right', 'text_domain'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('random_or_latest')); ?>"><?php _e('Show:', 'text_domain'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('random_or_latest')); ?>" name="<?php echo esc_attr($this->get_field_name('random_or_latest')); ?>" class="widefat">
                <option value="latest" <?php selected($random_or_latest, 'latest'); ?>><?php _e('Latest Posts', 'text_domain'); ?></option>
                <option value="random" <?php selected($random_or_latest, 'random'); ?>><?php _e('Random Posts', 'text_domain'); ?></option>
            </select>
        </p>
        <?php
    }
}

function register_new_recent_posts_select_categories_widget() {
    register_widget('New_Recent_Posts_Select_Categories_By_Thao_Marky');
}
add_action('widgets_init', 'register_new_recent_posts_select_categories_widget');

// Enqueue CSS file
function enqueue_new_recent_posts_styles() {
    wp_enqueue_style('new-recent-posts-widget-style', plugins_url('css/new-recent-posts-widget.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'enqueue_new_recent_posts_styles');

?>
