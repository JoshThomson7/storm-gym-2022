<?php
/**
 * APM Blogs
 *
 * Class in charge of services
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APM_Blogs {

    public function init() {

        add_action('wp_ajax_nopriv_apm_filter_blog', array($this, 'apm_filter_blog'));
        add_action('wp_ajax_apm_filter_blog', array($this, 'apm_filter_blog'));

    }

    /**
     * WP_Query
     * 
     * @param array $custom_args
     */
    public function get_blogs($custom_args = array()) {

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
    public function get_categories($custom_args = array()) {

        $default_args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 1
        );

        $args = wp_parse_args($custom_args, $default_args);
        $terms = get_terms('category', $args);

        return $terms;

    }

    /**
     * Filter Services.
     *
     * @since	1.0
     */
    public function apm_filter_blog() {

        // Security check.
        wp_verify_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M', 'ajax_security');

        parse_str($_POST['form_data'], $form_data);

        // Get data
        $blog_cat_id = isset($form_data['blog_cat']) && !empty($form_data['blog_cat']) ? $form_data['blog_cat'] : null;

        $args = array();

        if($blog_cat_id) {
            $args['cat'] = $blog_cat_id;
            $args['posts_per_page'] = 30;

            $featured_blog = $this->get_blogs(array(
                'posts_per_page' => 1
            ));
    
            $args['post__not_in'] = $featured_blog;
        }

        $get_blogs = $this->get_blogs($args);
        
        include blog_path() .'templates/blog-loop.php';

        wp_die();

    }

}

