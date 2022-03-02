<?php
/**
 * FL1 Blog Public
 *
 * Class in charge of FW Public facing side
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Blog_Public {

    public function init() {

        add_action('wp_enqueue_scripts', array($this, 'enqueue'));

        // Templates
        add_filter('page_template', array($this, 'blog_templates'));
        add_filter('single_template', array($this, 'blog_single_template' ));
        add_filter('category_template', array($this, 'blog_category_template'));
        add_filter('archive_template', array($this, 'blog_archive_template'));

    }

    public function enqueue() {

        wp_localize_script('custom-js', 'fl1_blogs_ajax_object', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'ajaxNonce' => wp_create_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M'),
            'jsPath' => FL1_BLOG_PATH.'assets/js/',
        ));
    
    }

    public function blog_pages() {
        $pages = array(
            array(
                'name'  => 'blog',
                'title' => 'Blog'
            )
        );
    
        $template = array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            'post_author' => 1
        );
    
        foreach( $pages as $page ) {
            $exists = get_page_by_title( $page['title'] );
    
            $my_page = array(
                'post_name'  => $page['name'],
                'post_title' => $page['title']
            );
    
            $my_page = array_merge( $my_page, $template );
    
            $id = ( $exists ? $exists->ID : wp_insert_post( $my_page ) );
    
            if( isset( $page['child'] ) ) {
                foreach( $page['child'] as $key => $value ) {
                    $child_id = get_page_by_title( $value );
                    $child_page = array(
                        'post_name'   => $key,
                        'post_title'  => $value,
                        'post_parent' => $id
                    );
                    $child_page = array_merge( $child_page, $template );
                    if( !isset( $child_id ) ) wp_insert_post( $child_page );
                }
            }
        }
    }

    /*
    *	Blog page
    */
    public function blog_templates($page_template) {
        global $post;

        if(is_page('blog')) {
            $page_template = FL1_BLOG_PATH . 'templates/blog.php';
        }

        return $page_template;

    }
    


    /*
    *	Single blog
    */
    public function blog_single_template($single_template) {
        global $post;

        if ($post->post_type === 'post') {
            $single_template = FL1_BLOG_PATH . 'templates/blog-single.php';
        }

        return $single_template;
    }
    

    /*
    *	Archive blog
    */
    public function blog_category_template( $archive_template ) {
        global $post;

        if ( is_category() ) {
            $archive_template = FL1_BLOG_PATH . 'templates/blog-archive.php';
        }

        return $archive_template;
    }

    

    /*
    *	Archive blog
    */
    public function blog_archive_template( $archive_template ) {
        global $post;

        if ( is_archive() ) {
            $archive_template = FL1_BLOG_PATH . 'templates/blog-archive.php';
        }

        return $archive_template;
    }

}

