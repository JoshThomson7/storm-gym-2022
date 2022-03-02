<?php
/**
 * APT Public
 *
 * Class in charge of APT Public facing side
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT_Public {

    public function init() {

        add_action('wp_enqueue_scripts', array($this, 'enqueue'));
        add_action('body_class', array($this, 'body_classes'), 20);

    }

    public function enqueue() {

        // Ajax
        wp_localize_script('custom-js', APT_SLUG.'_ajax_object', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce('$C.cGLu/1zxq%.KH}PjIKK|2_7WDN`x[vdhtF5GS4|+6%$wvG)2xZgJcWv3H2K_M'),
            'jsPath' => APT_URL.'assets/js/',
            'cssPath' => APT_URL.'assets/css/',
            'imgPath' => APT_URL.'assets/img/'
        ));

        wp_enqueue_script(APT_SLUG, APT_URL.'assets/js/apt.min.js');

        // Styles
        wp_enqueue_style(APT_SLUG, APT_URL.'assets/css/apt.min.css');

        // Enqueue React app
        //$this->embed_react_app('app/static', array('book'));

    }

    /**
	 * Returns body CSS class names.
	 *
	 * @since 1.0
     * @param array $classes
	 */
    public function body_classes($classes) {
        global $post;

        if(is_page()) {

            if($post->post_parent) {
                   
                $ancestors = get_post_ancestors( $post->ID );
                $ancestors = array_reverse($ancestors);
                   
                if ( !isset( $parents ) ) $parents = null;

                foreach($ancestors as $ancestor_id) {

                    $post_name = get_post_field('post_name', $ancestor_id);
                    $classes[] = APT_SLUG.'-'.$post_name;

                }

            }

            $current_post_name = get_post_field('post_name', $post->ID);
            $classes[] = APT_SLUG.'-'.$current_post_name;

        } elseif(is_singular(APT_Helpers::registered_post_types())) {

            $post_type = $post->post_type;
            $classes[] = APT_SLUG.'-single-'.$post_type;

        }

        return $classes;
    }

    /**
     * Embeds necessary production build files
     * 
     * @param string $path | Required - Files must be uploaded to a folder inside the active theme, ie: app/static
     * @param array $pages | Required - The ages in which to load the files
     */
    private function embed_react_app($path, $pages = array()) {

        if(empty($pages)) { return null; }

        foreach($pages as $page) {

            /**
             * Enqueue React app in "book" page
             */
            if(is_page($page)) {

                $CSSfiles = glob(TEMPLATEPATH.'/'.$path.'/css/*.css');

                $css_file_count = 0;
                foreach($CSSfiles as $CSSfilename) {
                    if(strpos($CSSfilename, '.css') && !strpos($CSSfilename, '.css.map')) {
                        $CSSfilename = basename($CSSfilename);
                        wp_enqueue_style('bodyset-book-'.$css_file_count, esc_url(get_stylesheet_directory_uri()).'/'.$path.'/css/' . $CSSfilename); // Header
                    }

                    $css_file_count++;
                }

                $JSfiles = glob(TEMPLATEPATH.'/'.$path.'/js/*.js');
                $react_js_to_load = '';

                $js_file_count = 0;
                foreach($JSfiles as $filename) {
                    if(strpos($filename,'.js')&&!strpos($filename,'.js.map')) {
                        $filename = basename($filename);
                        wp_enqueue_script('bodyset-book-react-'.$js_file_count, esc_url(get_stylesheet_directory_uri()).'/'.$path.'/js/' . $filename, '', '', true); // Footer
                    }

                    $js_file_count++;
                }

            }

        }

    }

}

