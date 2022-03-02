<?php
/**
 * FL1_Blog_Module Init
 *
 * Class in charge of initialising everything FW
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class FL1_Blog_Module {

    private $module_folder = 'blog';

    public function __construct() {

        $this->define_constants();
        $this->load_dependencies();

        add_action('after_setup_theme',	array($this, 'init'));

    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Setup constants.
     *
     * @access private
     * @since 1.0
     * @return void
     */
    private function define_constants() {

        $this->define('FL1_BLOG_VERSION', '2.0');
        $this->define('FL1_BLOG_SLUG', 'fl1-blog');
        $this->define('FL1_BLOG_PATH', get_stylesheet_directory().'/modules/'.$this->module_folder.'/');
        $this->define('FL1_BLOG_TEMPLATE_PATH', get_stylesheet_directory().'/modules/'.$this->module_folder.'/templates/');
        $this->define('FL1_BLOG_URL', get_stylesheet_directory_uri().'/modules/'.$this->module_folder.'/');

    }

    private function load_dependencies() {

        // Core
        include_once FL1_BLOG_PATH. 'functions/class-fl1-blog-public.php';
        include_once FL1_BLOG_PATH. 'functions/class-fl1-blogs.php';
        include_once FL1_BLOG_PATH. 'functions/class-fl1-blog.php';

    }

    public function init() {

        $blogs_public = new FL1_Blog_Public();
        $blogs_public->init();

        $blogs = new FL1_Blogs();
        $blogs->init();

    }

}

// Release the Kraken!
$fl1_blog_module = new FL1_Blog_Module();