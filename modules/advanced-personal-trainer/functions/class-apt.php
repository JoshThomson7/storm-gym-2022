<?php
/**
 * APT Init
 *
 * Class in charge of initialising everything APT
 */

 // Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class APT {

    public function __construct() {

        $this->define_constants();
        $this->load_dependencies();

        add_action('init', array($this, 'init_hooks'));
        add_action('after_setup_theme',	array($this, 'init'));
        
        // APT_WC_Cart::onLoad();

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

        $this->define('APT_VERSION', '1.0');
        $this->define('APT_PLUGIN_FOLDER', 'advanced-personal-trainer');
        $this->define('APT_SLUG', 'apt');
        $this->define('APT_PATH', get_stylesheet_directory().'/modules/'.APT_PLUGIN_FOLDER.'/');
        $this->define('APT_URL', get_stylesheet_directory_uri().'/modules/'.APT_PLUGIN_FOLDER.'/');
        $this->define('APT_GOOGLE_API_KEY', get_option('google_maps_api_key'));
        $this->define('APT_GOOGLE_GEOCODE_API_KEY', get_option('google_geocode_api_key'));
        $this->define('APT_REST_API_NAMESPACE', APT_SLUG);
        $this->define('APT_REST_API_URL', esc_url(home_url()).'/wp-json/'.APT_REST_API_NAMESPACE.'/');

    }
    
    /**
     * Loads all dependencies.
     *
     * @access private
     * @since 1.0
     * @return void
     */
    private function load_dependencies() {

        // Core
        include_once APT_PATH. 'functions/class-apt-helpers.php';
        include_once APT_PATH. 'functions/class-apt-cpt.php';
        include_once APT_PATH. 'functions/class-apt-public.php';
        
        // include_once APT_PATH. 'functions/class-apm-acf.php';
        include_once APT_PATH. 'functions/class-apt-templates.php';
        // include_once APT_PATH. 'functions/class-apm-email.php';
        // include_once APT_PATH. 'functions/class-apm-auth.php';

        // Data
        include_once APT_PATH. 'functions/class-apt-workouts.php';
        include_once APT_PATH. 'functions/class-apt-workout.php';
        include_once APT_PATH. 'functions/class-apt-video.php';

        // REST API
        include_once APT_PATH. 'functions/rest-api/class-apt-rest-api.php';
        include_once APT_PATH. 'functions/rest-api/class-apt-rest-workout-controller.php';

    }

    public function init_hooks() {

        // $apm_email = new APT_Email();
        // $apm_email->init();
        
    }

    public function init() {

        $apt_cpt = new APT_CPT();
        $apt_cpt->init();

        $apt_workouts = new APT_Workouts();
        $apt_workouts->init();

        // $apm_acf = new APT_ACF();
        // $apm_acf->init();

        $apt_public = new APT_Public();
        $apt_public->init();

        $apt_templates = new APT_Templates();
        $apt_templates->init();

        // $apm_auth = new APT_Auth();
        // $apm_auth->init();

        // $apm_locations = new APT_Locations();
        // $apm_locations->init();

        // $apm_team = new APT_Practitioners();
        // $apm_team->init();

        // $apm_services = new APT_Services();
        // $apm_services->init();

        // $apm_blogs = new APT_Blogs();
        // $apm_blogs->init();

        // $apm_wc_cart = new APT_WC_Cart();
        // $apm_wc_cart->init();

        // $apm_wc_checkout = new APT_WC_Checkout();
        // $apm_wc_checkout->init();

        // // $apm_wc_bookings = new APT_WC_Bookings();
        // // $apm_wc_bookings->init();

        $apt_rest_api = new APT_REST_API();
        $apt_rest_api->register_routes();

    }

}

// Release the Kraken!
$apm = new APT();

// function add_cors_http_header(){
//     header("Access-Control-Allow-Origin: *");
//     header("Access-Control-Allow-Credentials: true");
//     header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
//     header('Access-Control-Allow-Headers: X-Requested-With, privatekey');
// }
// add_action('init','add_cors_http_header');

