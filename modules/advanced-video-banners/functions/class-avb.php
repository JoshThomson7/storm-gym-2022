<?php
/**
 * Advanced Video Banners
 * Class in charge of initialising everything AVB
 * 
 * @package AVB
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AVB {

    public function __construct() {

        $this->module_constants();
        $this->module_dependencies();

        add_action('init', array($this, 'init_hooks'));
        add_action('after_setup_theme',	array($this, 'after_setup_theme'));

        // Crons
        // $apm_cron = new AVB_Cron();
        // add_action('apm_cron', array($apm_cron, 'cron_callback'), 10, 2);

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
    private function module_constants() {

        $this->define('AVB_VERSION', '2.0');
        $this->define('AVB_SLUG', 'avb');
        $this->define('AVB_PATH', get_stylesheet_directory().'/modules/advanced-video-banners/');
        $this->define('AVB_URL', get_stylesheet_directory_uri().'/modules/advanced-video-banners/');

    }
    
    /**
     * Loads all dependencies.
     *
     * @access private
     * @since 1.0
     * @return void
     */
    private function module_dependencies() {

        // Core
        include_once AVB_PATH. 'functions/class-avb-banner.php';

        // Banners
        include_once AVB_PATH. 'functions/class-avb-banner-image.php';
        include_once AVB_PATH. 'functions/class-avb-banner-youtube.php';
        include_once AVB_PATH. 'functions/class-avb-banner-vimeo.php';
        include_once AVB_PATH. 'functions/class-avb-banner-html-video.php';
        
    }

    /**
     * avb_banners()
     *
     * @param bool $type
    */
    public static function avb_banners() {
        include AVB_PATH.'templates/avb.php';
    }

    public function init_hooks() {

        // $apm_email = new AVB_Email();
        // $apm_email->init();
        
    }

    public function after_setup_theme() {

        //$wp_cpt = new WP_CPT();

    }

}

// Release the Kraken!
$avb = new AVB();
