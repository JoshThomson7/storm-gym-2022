<?php
/**
 * FL1_Blogs
 *
 * Class in charge of services
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Blogs {

    public function init() {

        add_action('wp_ajax_nopriv_blog_filters', array($this, 'blog_filters'));
        add_action('wp_ajax_blog_filters', array($this, 'blog_filters'));

    }

    /**
     * WP_Query
     * 
     * @param array $custom_args
     */
    public static function get_blogs($custom_args = array()) {

        $default_args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 15,
            'orderby' => 'date',
            'order' => 'desc',
            'fields' => 'ids'
        );

        $args = wp_parse_args($custom_args, $default_args);

        $posts = new WP_Query($args);
        return $posts->posts;

    }

    /**
     * Returns blog cats
     */
    public static function get_categories($custom_args = array()) {

        $default_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1
        );

        $args = wp_parse_args($custom_args, $default_args);
        $terms = get_terms('category', $args);

        return $terms;

    }

    public function get_filter_data($posted_data, $wpQueryArgs = array()) {

		$formData = isset($posted_data) && !empty($posted_data) ? $posted_data : '';
        $blog_cat_id = isset($formData['blog_cat']) && !empty($formData['blog_cat']) ? $formData['blog_cat'] : null;
        $wpQueryArgs = isset($wpQueryArgs) && is_array($wpQueryArgs) && !empty($wpQueryArgs) ? $wpQueryArgs : null;

		if ($blog_cat_id) {
            $args['orderby'] = 'name';
            $args['order'] = 'asc';
			$args['tax_query'] = array(
				array(
					array(
						'taxonomy' => 'category',
						'terms' => $blog_cat_id,
						'field' => 'term_id',
						'operator' => 'IN'
					)
				)
			);
            
        } else {

            $featured_blog = FL1_Blogs::get_blogs(array(
                'posts_per_page' => 1
            ));
    
            if(!empty($featured_blog)) {
                $featured_blog_id = reset($featured_blog);
                $args['post__not_in'] = array($featured_blog_id);
            }

        }
        
        if($wpQueryArgs) {
            $args = wp_parse_args($wpQueryArgs, $args);
        }

		$blogs = self::get_blogs($args);

		return array(
			'blogs' => $blogs,
			'posted' => array(
                'blog_cat_id' => $blog_cat_id,
                'wpQueryArgs' => $wpQueryArgs
			)
		);
	}

    /**
     * Filter Services.
     *
     * @since	1.0
     */
    public function blog_filters() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        $filtered = $this->get_filter_data($_POST['formData'], $_POST['wpQueryArgs']);

        include FL1_BLOG_PATH .'templates/blog-loop.php';

        wp_die();

    }

}

$blogs = new FL1_Blogs();
$blogs->init();
